<?php
namespace App\Actions\Search;

use App\Core\Request;
use App\Services\Api\TmdbService;
use App\Services\Api\JikanService;
use App\Core\Response;

class SearchAction
{
  public function handle(Request $request): void
  {
    $searchQuery = $request->getQuery('searchQuery');

    if( !$searchQuery ) Response::badRequest('searchQuery is required.');

    $tmdb = new TmdbService();
    $jikan = new JikanService();

    Response::json([
      'movies' => $tmdb->searchMovies($searchQuery),
      'tvshows' => $tmdb->searchTvShows($searchQuery),
      'anime' => $jikan->searchAnime($searchQuery),
    ]);
  }
}