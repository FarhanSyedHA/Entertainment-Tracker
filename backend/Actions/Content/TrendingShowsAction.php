<?php
namespace App\Actions\Content;

use App\Core\Request;
use App\Core\Response;
use App\Services\Api\TmdbService;
use App\Services\Api\JikanService;

class TrendingShowsAction
{
  public function handle(Request $request): void
  {
    $tmdb = new TmdbService();
    $jikan = new JikanService();

    $movies = $tmdb->getTrendingMovies();
    $tvShows = $tmdb->getTrendingTvShows();
    $anime = $jikan->getTrendingAnime();

    Response::json([
      'movies' => $movies,
      'tvshows' => $tvShows,
      'anime' => $anime,
    ]);
  }
}
