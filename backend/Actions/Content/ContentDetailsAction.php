<?php
namespace App\Actions\Content;

use App\Core\Request;
use App\Core\Response;
use App\Services\ContentService;

class ContentDetailsAction
{
  public function handle(Request $request): void
  {
    $id = $request->getQuery('id');
    $type = $request->getQuery('type');

    if (!$id || !$type) {
      Response::badRequest('id and type are required');
    }

    if (!in_array($type, ['movie', 'tvshows', 'anime'], true)) {
      Response::badRequest('unknown type');
    }

    $details = (new ContentService())->getDetailsWithSeasons((int) $id, $type);

    if (empty($details)) {
      Response::json(['error' => 'Details not available'], 502);
    }

    Response::json($details);
  }
}
