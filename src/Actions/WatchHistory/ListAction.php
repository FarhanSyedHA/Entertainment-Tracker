<?php

namespace App\Actions\WatchHistory;

use App\Core\Request;
use App\Core\Response;
use App\Services\WatchHistoryService;

class ListAction
{
    public function __invoke(Request $request): Response
    {
        $type = $request->getQuery('type', 'all');

        $service = new WatchHistoryService();
        $items = $service->getList($request->user->id, $type);

        return Response::json(['items' => $items]);
    }
}
