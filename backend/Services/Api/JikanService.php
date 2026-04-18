<?php
namespace App\Services\Api;

use App\Adapters\JikanAdapter;
use App\Interfaces\AnimeSourceInterface;

class JikanService implements AnimeSourceInterface
{
  private string $baseJikanUrl;

  public function __construct()
  {
    $this->baseJikanUrl = 'https://api.jikan.moe/v4';
  }

  public function getTrendingAnime(): array
  {
    $raw = HttpClient::get($this->baseJikanUrl . '/top/anime?filter=airing');
    return JikanAdapter::toShows($raw);
  }

  public function getAnimeDetails(int $id): array
  {
    $raw = HttpClient::get($this->baseJikanUrl . '/anime/' . $id . '/full');
    return JikanAdapter::toDetails($raw);
  }

  public function searchAnime(string $searchQuery): array
  {
    $searchQuery = urlencode($searchQuery);
    $raw = HttpClient::get($this->baseJikanUrl . '/anime?q=' . $searchQuery . '&sfw=true&limit=25');
    return JikanAdapter::toShows($raw);
  }

  public function getAnimeEpisodes(int $id): array
  {
    $all = [];
    $page = 1;
    $maxPages = 20;
    while ($page <= $maxPages) {
      $raw = HttpClient::get($this->baseJikanUrl . '/anime/' . $id . '/episodes?page=' . $page);
      $items = $raw['data'] ?? [];
      if (empty($items)) break;
      foreach ($items as $ep) $all[] = $ep;
      $hasNext = $raw['pagination']['has_next_page'] ?? false;
      if (!$hasNext) break;
      $page++;
      usleep(350000);
    }
    return $all;
  }
}