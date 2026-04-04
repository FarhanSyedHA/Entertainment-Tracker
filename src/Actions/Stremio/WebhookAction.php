<?php

namespace App\Actions\Stremio;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\HttpException;
use App\Services\ContentService;
use App\Services\WatchHistoryService;

class WebhookAction
{
    public function __invoke(Request $request): Response
    {
        $body = $request->getBody();
        $imdbId = $body['imdb_id'] ?? null;
        $contentType = $body['content_type'] ?? 'movie';
        $seasonNum = $body['season'] ?? null;
        $episodeNum = $body['episode'] ?? null;
        $progressPercent = $body['progress_percent'] ?? null;
        $lastPositionSeconds = $body['last_position_seconds'] ?? null;
        $durationSeconds = $body['duration_seconds'] ?? null;

        if (!$imdbId) {
            throw new HttpException('imdb_id is required', 422);
        }

        $db = Database::getInstance();

        // Find content by IMDB ID
        $stmt = $db->prepare('SELECT * FROM content WHERE imdb_id = :imdb_id');
        $stmt->execute(['imdb_id' => $imdbId]);
        $contentRow = $stmt->fetch();

        if (!$contentRow) {
            // Content not cached yet — try to fetch via TMDB
            $contentService = new ContentService();
            $tmdbData = $this->lookupTmdbByImdb($imdbId, $contentType);

            if (!$tmdbData) {
                throw new HttpException('Could not find content for IMDB ID: ' . $imdbId, 404);
            }

            $content = $contentService->fetchAndCache($contentType, $tmdbData['tmdb_id']);
            $contentId = $content->id;
        } else {
            $contentId = (int) $contentRow['id'];
        }

        // Find episode if TV/anime
        $episodeId = null;
        if ($contentType !== 'movie' && $seasonNum && $episodeNum) {
            $stmt = $db->prepare('
                SELECT e.id FROM episodes e
                INNER JOIN seasons s ON s.id = e.season_id
                WHERE s.content_id = :content_id
                AND s.season_number = :season_num
                AND e.episode_number = :episode_num
            ');
            $stmt->execute([
                'content_id' => $contentId,
                'season_num' => $seasonNum,
                'episode_num' => $episodeNum,
            ]);
            $epRow = $stmt->fetch();
            $episodeId = $epRow ? (int) $epRow['id'] : null;
        }

        // Add or update watch history
        $whService = new WatchHistoryService();
        $entry = $whService->add($request->user->id, $contentId, $episodeId, 'in_progress');

        // Update progress if provided
        if ($progressPercent !== null) {
            $entry = $whService->updateProgress(
                $request->user->id,
                $entry->id,
                (int) $progressPercent,
                $lastPositionSeconds ? (int) $lastPositionSeconds : null,
                $durationSeconds ? (int) $durationSeconds : null
            );
        }

        return Response::json([
            'message' => 'Watch event logged',
            'watch_history' => $entry->toArray(),
        ]);
    }

    private function lookupTmdbByImdb(string $imdbId, string $contentType): ?array
    {
        $apiKey = getenv('TMDB_API_KEY') ?: '';
        $url = "https://api.themoviedb.org/3/find/{$imdbId}?" . http_build_query([
            'api_key' => $apiKey,
            'external_source' => 'imdb_id',
        ]);

        $response = file_get_contents($url);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (!$data) return null;

        if ($contentType === 'movie' && !empty($data['movie_results'])) {
            return ['tmdb_id' => $data['movie_results'][0]['id']];
        }

        if ($contentType === 'tv' && !empty($data['tv_results'])) {
            return ['tmdb_id' => $data['tv_results'][0]['id']];
        }

        return null;
    }
}
