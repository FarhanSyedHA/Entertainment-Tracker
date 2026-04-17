<?php
namespace App\Actions\WatchHistory;

use App\Core\Request;
use App\Core\Response;
use App\Services\WatchService;

class WatchStatusAction
{
  public function handle(Request $request): void
  {
    $userId = $request->getUserId();
    if (!$userId) Response::unauthorized();

    $body = $request->getBody();
    $items = $body['items'] ?? [];

    Response::json(['watched' => WatchService::getWatchedMap($userId, $items)]);
  }
}
