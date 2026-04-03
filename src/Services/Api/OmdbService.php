<?php

namespace App\Services\Api;

class OmdbService
{
    private const BASE_URL = 'https://www.omdbapi.com/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('OMDB_API_KEY') ?: '';
    }

    public function getByImdbId(string $imdbId): ?array
    {
        if (empty($imdbId)) {
            return null;
        }

        $url = self::BASE_URL . '?' . http_build_query([
            'apikey' => $this->apiKey,
            'i' => $imdbId,
        ]);

        $data = HttpClient::get($url);
        if (!$data || ($data['Response'] ?? '') !== 'True') {
            return null;
        }

        return [
            'imdb_id' => $data['imdbID'] ?? null,
            'rating_imdb' => $this->parseFloat($data['imdbRating'] ?? null),
            'rating_rt' => $this->parseRottenTomatoes($data['Ratings'] ?? []),
            'rating_metacritic' => $this->parseInt($data['Metasccore'] ?? $data['Metascore'] ?? null),
        ];
    }

    private function parseRottenTomatoes(array $ratings): ?string
    {
        foreach ($ratings as $rating) {
            if ($rating['Source'] === 'Rotten Tomatoes') {
                return $rating['Value'];
            }
        }
        return null;
    }

    private function parseFloat(?string $value): ?float
    {
        if ($value === null || $value === 'N/A') {
            return null;
        }
        return (float) $value;
    }

    private function parseInt(?string $value): ?int
    {
        if ($value === null || $value === 'N/A') {
            return null;
        }
        return (int) $value;
    }
}
