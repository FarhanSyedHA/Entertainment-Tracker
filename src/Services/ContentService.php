<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Entities\Content;
use App\Entities\Season;
use App\Entities\Episode;
use App\Services\Api\TmdbService;
use App\Services\Api\JikanService;
use App\Services\Api\OmdbService;

class ContentService
{
    private TmdbService $tmdb;
    private JikanService $jikan;
    private OmdbService $omdb;

    public function __construct()
    {
        $this->tmdb = new TmdbService();
        $this->jikan = new JikanService();
        $this->omdb = new OmdbService();
    }

    public function search(string $query, string $type = 'all'): array
    {
        $results = [];

        if ($type === 'all' || $type === 'movie' || $type === 'tv') {
            $tmdbResults = $this->tmdb->searchMulti($query);
            if ($type !== 'all') {
                $tmdbResults = array_filter($tmdbResults, fn($r) => $r['content_type'] === $type);
            }
            $results = array_merge($results, array_values($tmdbResults));
        }

        if ($type === 'all' || $type === 'anime') {
            $animeResults = $this->jikan->searchAnime($query);
            $results = array_merge($results, $animeResults);
        }

        return $results;
    }

    public function fetchAndCache(string $contentType, int $externalId): Content
    {
        // Check if already cached
        $existing = $this->findCached($contentType, $externalId);
        if ($existing) {
            return $existing;
        }

        // Fetch from API
        $data = match ($contentType) {
            'movie' => $this->tmdb->getMovie($externalId),
            'tv' => $this->tmdb->getTvShow($externalId),
            'anime' => $this->jikan->getAnime($externalId),
            default => null,
        };

        if (!$data) {
            throw new NotFoundException('Content not found on external API');
        }

        // Enrich with OMDB ratings if we have an IMDB ID
        if (!empty($data['imdb_id'])) {
            $omdbData = $this->omdb->getByImdbId($data['imdb_id']);
            if ($omdbData) {
                $data['rating_imdb'] = $omdbData['rating_imdb'] ?? $data['rating_imdb'] ?? null;
                $data['rating_rt'] = $omdbData['rating_rt'] ?? null;
                $data['rating_metacritic'] = $omdbData['rating_metacritic'] ?? null;
            }
        }

        // Insert into content table
        $content = $this->insertContent($data);

        // For TV shows, cache seasons and episodes
        if ($contentType === 'tv' && !empty($data['seasons'])) {
            $this->cacheTvSeasons($content->id, $content->tmdbId, $data['seasons']);
        }

        // For anime, cache episodes as a single season
        if ($contentType === 'anime' && $data['total_episodes']) {
            $this->cacheAnimeEpisodes($content->id, $externalId, $data['total_episodes']);
        }

        return $content;
    }

    public function getById(int $id): ?Content
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM content WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Content::fromRow($row) : null;
    }

    public function getSeasons(int $contentId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM seasons WHERE content_id = :content_id ORDER BY season_number');
        $stmt->execute(['content_id' => $contentId]);
        return array_map(fn($row) => Season::fromRow($row), $stmt->fetchAll());
    }

    public function getEpisodes(int $seasonId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM episodes WHERE season_id = :season_id ORDER BY episode_number');
        $stmt->execute(['season_id' => $seasonId]);
        return array_map(fn($row) => Episode::fromRow($row), $stmt->fetchAll());
    }

    private function findCached(string $contentType, int $externalId): ?Content
    {
        $db = Database::getInstance();

        if ($contentType === 'anime') {
            $stmt = $db->prepare('SELECT * FROM content WHERE jikan_id = :id');
        } else {
            $stmt = $db->prepare('SELECT * FROM content WHERE tmdb_id = :id AND content_type = :type');
            $stmt->execute(['id' => $externalId, 'type' => $contentType]);
            $row = $stmt->fetch();
            return $row ? Content::fromRow($row) : null;
        }

        $stmt->execute(['id' => $externalId]);
        $row = $stmt->fetch();
        return $row ? Content::fromRow($row) : null;
    }

    private function insertContent(array $data): Content
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            INSERT INTO content (
                content_type, tmdb_id, jikan_id, imdb_id, title, original_title,
                overview, poster_url, backdrop_url, release_date, end_date,
                runtime_minutes, status, rating_tmdb, rating_imdb, rating_rt,
                rating_metacritic, genres, total_seasons, total_episodes, metadata_updated_at
            ) VALUES (
                :content_type, :tmdb_id, :jikan_id, :imdb_id, :title, :original_title,
                :overview, :poster_url, :backdrop_url, :release_date, :end_date,
                :runtime_minutes, :status, :rating_tmdb, :rating_imdb, :rating_rt,
                :rating_metacritic, :genres, :total_seasons, :total_episodes, NOW()
            )
        ');

        $stmt->execute([
            'content_type' => $data['content_type'],
            'tmdb_id' => $data['tmdb_id'] ?? null,
            'jikan_id' => $data['jikan_id'] ?? null,
            'imdb_id' => $data['imdb_id'] ?? null,
            'title' => $data['title'],
            'original_title' => $data['original_title'] ?? null,
            'overview' => $data['overview'] ?? null,
            'poster_url' => $data['poster_url'] ?? null,
            'backdrop_url' => $data['backdrop_url'] ?? null,
            'release_date' => $data['release_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'runtime_minutes' => $data['runtime_minutes'] ?? null,
            'status' => $data['status'] ?? null,
            'rating_tmdb' => $data['rating_tmdb'] ?? null,
            'rating_imdb' => $data['rating_imdb'] ?? null,
            'rating_rt' => $data['rating_rt'] ?? null,
            'rating_metacritic' => $data['rating_metacritic'] ?? null,
            'genres' => !empty($data['genres']) ? json_encode($data['genres']) : null,
            'total_seasons' => $data['total_seasons'] ?? null,
            'total_episodes' => $data['total_episodes'] ?? null,
        ]);

        return $this->getById((int) $db->lastInsertId());
    }

    private function cacheTvSeasons(int $contentId, int $tmdbId, array $seasons): void
    {
        $db = Database::getInstance();

        foreach ($seasons as $seasonData) {
            $stmt = $db->prepare('
                INSERT INTO seasons (content_id, season_number, name, overview, poster_url, air_date, episode_count)
                VALUES (:content_id, :season_number, :name, :overview, :poster_url, :air_date, :episode_count)
            ');
            $stmt->execute([
                'content_id' => $contentId,
                'season_number' => $seasonData['season_number'],
                'name' => $seasonData['name'] ?? null,
                'overview' => $seasonData['overview'] ?? null,
                'poster_url' => $seasonData['poster_url'] ?? null,
                'air_date' => $seasonData['air_date'] ?? null,
                'episode_count' => $seasonData['episode_count'] ?? null,
            ]);

            $seasonId = (int) $db->lastInsertId();

            // Fetch episodes for this season
            $episodes = $this->tmdb->getTvSeason($tmdbId, $seasonData['season_number']);
            foreach ($episodes as $epData) {
                $epStmt = $db->prepare('
                    INSERT INTO episodes (season_id, episode_number, name, overview, still_url, air_date, runtime_minutes)
                    VALUES (:season_id, :episode_number, :name, :overview, :still_url, :air_date, :runtime_minutes)
                ');
                $epStmt->execute([
                    'season_id' => $seasonId,
                    'episode_number' => $epData['episode_number'],
                    'name' => $epData['name'] ?? null,
                    'overview' => $epData['overview'] ?? null,
                    'still_url' => $epData['still_url'] ?? null,
                    'air_date' => $epData['air_date'] ?? null,
                    'runtime_minutes' => $epData['runtime_minutes'] ?? null,
                ]);
            }
        }
    }

    private function cacheAnimeEpisodes(int $contentId, int $malId, int $totalEpisodes): void
    {
        $db = Database::getInstance();

        // Create a single season for anime
        $stmt = $db->prepare('
            INSERT INTO seasons (content_id, season_number, name, episode_count)
            VALUES (:content_id, 1, :name, :episode_count)
        ');
        $stmt->execute([
            'content_id' => $contentId,
            'name' => 'Season 1',
            'episode_count' => $totalEpisodes,
        ]);
        $seasonId = (int) $db->lastInsertId();

        // Fetch episodes from Jikan (paginated, 100 per page)
        $page = 1;
        $fetched = 0;
        while ($fetched < $totalEpisodes) {
            $episodes = $this->jikan->getAnimeEpisodes($malId, $page);
            if (empty($episodes)) {
                break;
            }

            foreach ($episodes as $epData) {
                $epStmt = $db->prepare('
                    INSERT INTO episodes (season_id, episode_number, name, air_date)
                    VALUES (:season_id, :episode_number, :name, :air_date)
                ');
                $epStmt->execute([
                    'season_id' => $seasonId,
                    'episode_number' => $epData['episode_number'],
                    'name' => $epData['name'] ?? null,
                    'air_date' => $epData['air_date'] ?? null,
                ]);
                $fetched++;
            }

            $page++;
        }
    }
}
