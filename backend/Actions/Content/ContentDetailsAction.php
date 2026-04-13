<?php
namespace App\Actions\Content;

use App\Core\Request;
use App\Core\Response;
use App\Services\Api\TmdbService;
use App\Services\Api\JikanService;

class ContentDetailsAction
{
  public function handle(Request $request): void
  {
    $id = $request->getQuery('id');
    $type = $request->getQuery('type');

    if (!$id || !$type) {
      Response::badRequest('id and type are required');
    }

    $details = match ($type) {
      'movie' => (new TmdbService())->getMovieDetails((int) $id),
      'tvshows' => (new TmdbService())->getTvShowDetails((int) $id),
      'anime' => (new JikanService())->getAnimeDetails((int) $id),
      default => null,
    };

    if ($details === null) {
      Response::badRequest('unknown type');
    }

    Response::json($details);
  }
}