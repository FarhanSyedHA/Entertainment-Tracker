<?php
namespace App\Adapters;

class TmdbAdapter
{
  public static function toShows(array $raw, string $type): array
  {
    $results = $raw['results'] ?? [];
    return array_map(fn($item) => [
      'id' => $item['id'],
      'title' => $item['title'] ?? $item['name'],
      'poster' => $item['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : null,
      'rating' => $item['vote_average'],
      'year' => substr($item['release_date'] ?? $item['first_air_date'] ?? '', 0, 4),
      'type' => $type,
    ], $results);
  }

  public static function toDetails(array $raw, string $type): array
  {
    return [
      'id' => $raw['id'],
      'title' => $raw['title'] ?? $raw['name'],
      'poster' => $raw['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $raw['poster_path'] : null,
      'backdrop' => $raw['backdrop_path'] ? 'https://image.tmdb.org/t/p/original' . $raw['backdrop_path'] : null,
      'rating' => $raw['vote_average'] ?? 0,
      'year' => substr($raw['release_date'] ?? $raw['first_air_date'] ?? '', 0, 4),
      'type' => $type,
      'overview' => $raw['overview'] ?? '',
      'genres' => array_map(fn($g) => $g['name'], $raw['genres'] ?? []),
      'runtime' => $raw['runtime'] ?? ($raw['episode_run_time'][0] ?? null),
    ];
  }

  public static function toSeasonSummaries(array $rawSeasons): array
  {
    $filtered = array_filter($rawSeasons, fn($s) => ($s['season_number'] ?? 0) >= 1);
    return array_values(array_map(fn($s) => [
      'season_number' => (int) $s['season_number'],
      'name' => $s['name'] ?? ('Season ' . $s['season_number']),
      'episode_count' => (int) ($s['episode_count'] ?? 0),
    ], $filtered));
  }

  public static function toEpisodes(array $rawEpisodes): array
  {
    return array_values(array_map(fn($e) => [
      'episode_number' => (int) ($e['episode_number'] ?? 0),
      'title' => $e['name'] ?? null,
      'runtime_seconds' => !empty($e['runtime']) ? ((int) $e['runtime']) * 60 : null,
      'air_date' => $e['air_date'] ?? null,
    ], $rawEpisodes));
  }
}