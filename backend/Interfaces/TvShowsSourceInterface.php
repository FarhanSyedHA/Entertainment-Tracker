<?php
namespace App\Interfaces;

interface TvShowsSourceInterface
{
  public function getTrendingTvShows(): array;

  public function getTvShowDetails(int $id): array;

  public function searchTvShows(string $searchQuery): array;
}