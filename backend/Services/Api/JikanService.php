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
}