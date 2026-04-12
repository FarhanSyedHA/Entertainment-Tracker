<?php
namespace App\Services\Api;

use App\Adapters\JikanAdapter;

class JikanService
{
  private string $baseJikanUrl;

  public function __construct()
  {
    $this->baseJikanUrl = 'https://api.jikan.moe/v4';
  }

  public function getTrendingAnime()
  {
    $raw = HttpClient::get($this->baseJikanUrl . '/top/anime?filter=airing');
    return JikanAdapter::toShows($raw);
  }

  public function getAnimeDetails(int $id)
  {
    $raw = HttpClient::get($this->baseJikanUrl . '/anime/' . $id . '/full');
    return JikanAdapter::toDetails($raw);
  }
}