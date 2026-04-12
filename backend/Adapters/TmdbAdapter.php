<?php
namespace App\Adapters;

class TmdbAdapter
{
  public static function toShows(array $raw, string $type): array
  {
    $results = $raw['results'] ?? [];
    return array_map(fn($item) => [
      'id' => $item['id'],
      'title' => $item['title'] ?? $item['name'],   // movies use 'title', tv uses 'name'
      'poster' => $item['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : null,
      'rating' => $item['vote_average'],
      'year' => substr($item['release_date'] ?? $item['first_air_date'] ?? '', 0, 4),
      'type' => $type,
    ], $results);
  }
}
