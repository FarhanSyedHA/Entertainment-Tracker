<?php
namespace App\Adapters;

class JikanAdapter
{
  public static function toShows(array $raw): array
  {
    $results = $raw['data'] ?? [];
    $shows = array_map(fn($item) => [
      'id' => $item['mal_id'],
      'title' => $item['title'],
      'poster' => $item['images']['jpg']['image_url'] ?? null,
      'rating' => $item['score'] ?? 0,
      'year' => isset($item['aired']['from']) ? substr($item['aired']['from'], 0, 4) : '',
      'type' => 'anime',
    ], $results);
    //jikanadapter was returning duplicates
    $seen = [];
    return array_values(array_filter($shows, function($s) use (&$seen) { //its like .filter in js, if function is true row is added to seen, function uses $seen's reference. so changing it in side function changes the outer seen.
      if (isset($seen[$s['id']])) return false;
      $seen[$s['id']] = true;
      return true;
    }));
  }

  public static function toDetails(array $raw): array
  {
    $item = $raw['data'] ?? [];
    return [
      'id' => $item['mal_id'] ?? null,
      'title' => $item['title'] ?? '',
      'poster' => $item['images']['jpg']['large_image_url'] ?? $item['images']['jpg']['image_url'] ?? null,
      'backdrop' => $item['trailer']['images']['maximum_image_url'] ?? null,
      'rating' => $item['score'] ?? 0,
      'year' => isset($item['aired']['from']) ? substr($item['aired']['from'], 0, 4) : '',
      'type' => 'anime',
      'overview' => $item['synopsis'] ?? '',
      'genres' => array_map(fn($g) => $g['name'], $item['genres'] ?? []),
      'runtime' => $item['duration'] ?? null,
      'total_episodes' => isset($item['episodes']) ? (int) $item['episodes'] : null,
    ];
  }

  public static function toEpisodes(array $rawEpisodes): array
  {
    return array_values(array_map(fn($e) => [
      'episode_number' => (int) ($e['mal_id'] ?? 0),
      'title' => $e['title'] ?? null,
      'runtime_seconds' => null,
      'air_date' => $e['aired'] ?? null,
    ], $rawEpisodes));
  }
}