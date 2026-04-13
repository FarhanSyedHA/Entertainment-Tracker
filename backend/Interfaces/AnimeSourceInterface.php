<?php
namespace App\Interfaces;

interface AnimeSourceInterface
{
  public function getTrendingAnime(): array;

  public function getAnimeDetails(int $id): array;

  public function searchAnime(string $searchQuery): array;
}