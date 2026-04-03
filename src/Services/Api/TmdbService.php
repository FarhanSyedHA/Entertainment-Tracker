<?php

namespace App\Services\Api;

class TmdbService
{
    private const BASE_URL = 'https://api.themoviedb.org/3';
    private const IMAGE_BASE = 'https://image.tmdb.org/t/p/w500';
    private const BACKDROP_BASE = 'https://image.tmdb.org/t/p/w1280';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('TMDB_API_KEY') ?: '';
    }

    public function searchMulti(string $query, int $page = 1): array
    {
        $url = self::BASE_URL . '/search/multi?' . http_build_query([
            'api_key' => $this->apiKey,
            'query' => $query,
            'page' => $page,
            'include_adult' => 'false',
        ]);

        $data = HttpClient::get($url);
        if (!$data || empty($data['results'])) {
            return [];
        }

        $results = [];
        foreach ($data['results'] as $item) {
            if (!in_array($item['media_type'], ['movie', 'tv'])) {
                continue;
            }
            $results[] = $this->normalizeResult($item);
        }

        return $results;
    }

    public function getMovie(int $tmdbId): ?array
    {
        $url = self::BASE_URL . '/movie/' . $tmdbId . '?' . http_build_query([
            'api_key' => $this->apiKey,
            'append_to_response' => 'external_ids',
        ]);

        $data = HttpClient::get($url);
        if (!$data || isset($data['status_code'])) {
            return null;
        }

        return [
            'content_type' => 'movie',
            'tmdb_id' => $data['id'],
            'imdb_id' => $data['external_ids']['imdb_id'] ?? null,
            'title' => $data['title'],
            'original_title' => $data['original_title'],
            'overview' => $data['overview'],
            'poster_url' => $data['poster_path'] ? self::IMAGE_BASE . $data['poster_path'] : null,
            'backdrop_url' => $data['backdrop_path'] ? self::BACKDROP_BASE . $data['backdrop_path'] : null,
            'release_date' => $data['release_date'] ?: null,
            'runtime_minutes' => $data['runtime'],
            'status' => $data['status'],
            'rating_tmdb' => $data['vote_average'],
            'genres' => array_map(fn($g) => $g['name'], $data['genres'] ?? []),
        ];
    }

    public function getTvShow(int $tmdbId): ?array
    {
        $url = self::BASE_URL . '/tv/' . $tmdbId . '?' . http_build_query([
            'api_key' => $this->apiKey,
            'append_to_response' => 'external_ids',
        ]);

        $data = HttpClient::get($url);
        if (!$data || isset($data['status_code'])) {
            return null;
        }

        return [
            'content_type' => 'tv',
            'tmdb_id' => $data['id'],
            'imdb_id' => $data['external_ids']['imdb_id'] ?? null,
            'title' => $data['name'],
            'original_title' => $data['original_name'],
            'overview' => $data['overview'],
            'poster_url' => $data['poster_path'] ? self::IMAGE_BASE . $data['poster_path'] : null,
            'backdrop_url' => $data['backdrop_path'] ? self::BACKDROP_BASE . $data['backdrop_path'] : null,
            'release_date' => $data['first_air_date'] ?: null,
            'end_date' => $data['last_air_date'] ?: null,
            'status' => $data['status'],
            'rating_tmdb' => $data['vote_average'],
            'genres' => array_map(fn($g) => $g['name'], $data['genres'] ?? []),
            'total_seasons' => $data['number_of_seasons'],
            'total_episodes' => $data['number_of_episodes'],
            'seasons' => array_map(fn($s) => [
                'season_number' => $s['season_number'],
                'name' => $s['name'],
                'overview' => $s['overview'],
                'poster_url' => $s['poster_path'] ? self::IMAGE_BASE . $s['poster_path'] : null,
                'air_date' => $s['air_date'] ?: null,
                'episode_count' => $s['episode_count'],
            ], $data['seasons'] ?? []),
        ];
    }

    public function getTvSeason(int $tmdbId, int $seasonNumber): ?array
    {
        $url = self::BASE_URL . '/tv/' . $tmdbId . '/season/' . $seasonNumber . '?' . http_build_query([
            'api_key' => $this->apiKey,
        ]);

        $data = HttpClient::get($url);
        if (!$data || isset($data['status_code'])) {
            return null;
        }

        return array_map(fn($ep) => [
            'episode_number' => $ep['episode_number'],
            'name' => $ep['name'],
            'overview' => $ep['overview'],
            'still_url' => $ep['still_path'] ? self::IMAGE_BASE . $ep['still_path'] : null,
            'air_date' => $ep['air_date'] ?: null,
            'runtime_minutes' => $ep['runtime'] ?? null,
        ], $data['episodes'] ?? []);
    }

    private function normalizeResult(array $item): array
    {
        $isMovie = $item['media_type'] === 'movie';

        return [
            'content_type' => $isMovie ? 'movie' : 'tv',
            'tmdb_id' => $item['id'],
            'title' => $isMovie ? ($item['title'] ?? '') : ($item['name'] ?? ''),
            'overview' => $item['overview'] ?? '',
            'poster_url' => $item['poster_path'] ? self::IMAGE_BASE . $item['poster_path'] : null,
            'release_date' => $isMovie ? ($item['release_date'] ?? null) : ($item['first_air_date'] ?? null),
            'rating_tmdb' => $item['vote_average'] ?? null,
        ];
    }
}
