<?php
namespace App\Interfaces;

interface MoviesSourceInterface
{
  public function getTrendingMovies(): array;

  public function getMovieDetails(int $id): array;

  public function searchMovies(string $searchQuery): array;
}