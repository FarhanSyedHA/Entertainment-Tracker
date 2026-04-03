<?php

namespace App\Services\Api;

class JikanService
{
    private const BASE_URL = 'https://api.jikan.moe/v4';

    public function searchAnime(string $query, int $page = 1): array
    {
        $url = self::BASE_URL . '/anime?' . http_build_query([
            'q' => $query,
            'page' => $page,
            'sfw' => 'true',
        ]);

        $data = HttpClient::get($url);
        if (!$data || empty($data['data'])) {
            return [];
        }

        return array_map(fn($item) => $this->normalizeResult($item), $data['data']);
    }

    public function getAnime(int $malId): ?array
    {
        $url = self::BASE_URL . '/anime/' . $malId . '/full';

        $data = HttpClient::get($url);
        if (!$data || empty($data['data'])) {
            return null;
        }

        $anime = $data['data'];

        return [
            'content_type' => 'anime',
            'jikan_id' => $anime['mal_id'],
            'title' => $anime['title'],
            'original_title' => $anime['title_japanese'] ?? null,
            'overview' => $anime['synopsis'],
            'poster_url' => $anime['images']['jpg']['large_image_url'] ?? $anime['images']['jpg']['image_url'] ?? null,
            'release_date' => $anime['aired']['from'] ? date('Y-m-d', strtotime($anime['aired']['from'])) : null,
            'end_date' => $anime['aired']['to'] ? date('Y-m-d', strtotime($anime['aired']['to'])) : null,
            'runtime_minutes' => $anime['duration'] ? $this->parseDuration($anime['duration']) : null,
            'status' => $anime['status'],
            'rating_tmdb' => $anime['score'] ?? null,
            'genres' => array_map(fn($g) => $g['name'], array_merge($anime['genres'] ?? [], $anime['themes'] ?? [])),
            'total_episodes' => $anime['episodes'],
        ];
    }

    public function getAnimeEpisodes(int $malId, int $page = 1): array
    {
        $url = self::BASE_URL . '/anime/' . $malId . '/episodes?' . http_build_query([
            'page' => $page,
        ]);

        $data = HttpClient::get($url);
        if (!$data || empty($data['data'])) {
            return [];
        }

        return array_map(fn($ep) => [
            'episode_number' => $ep['mal_id'],
            'name' => $ep['title'] ?? $ep['title_romanji'] ?? null,
            'air_date' => $ep['aired'] ? date('Y-m-d', strtotime($ep['aired'])) : null,
        ], $data['data']);
    }

    private function normalizeResult(array $item): array
    {
        return [
            'content_type' => 'anime',
            'jikan_id' => $item['mal_id'],
            'title' => $item['title'],
            'overview' => $item['synopsis'] ?? '',
            'poster_url' => $item['images']['jpg']['large_image_url'] ?? $item['images']['jpg']['image_url'] ?? null,
            'release_date' => $item['aired']['from'] ? date('Y-m-d', strtotime($item['aired']['from'])) : null,
            'rating_tmdb' => $item['score'] ?? null,
            'total_episodes' => $item['episodes'] ?? null,
        ];
    }

    private function parseDuration(string $duration): ?int
    {
        // "24 min per ep" -> 24
        if (preg_match('/(\d+)\s*min/', $duration, $matches)) {
            return (int) $matches[1];
        }
        // "1 hr 30 min" -> 90
        if (preg_match('/(\d+)\s*hr\s*(\d+)\s*min/', $duration, $matches)) {
            return ((int) $matches[1] * 60) + (int) $matches[2];
        }
        if (preg_match('/(\d+)\s*hr/', $duration, $matches)) {
            return (int) $matches[1] * 60;
        }
        return null;
    }
}
