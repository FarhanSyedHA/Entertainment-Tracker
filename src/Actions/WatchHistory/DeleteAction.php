<?php

namespace App\Actions\WatchHistory;

use App\Core\Request;
use App\Core\Response;
use App\Services\WatchHistoryService;

class DeleteAction
{
    public function __invoke(Request $request): Response
    {
        $id = (int) $request->getParam('id');

        $service = new WatchHistoryService();
        $service->delete($request->user->id, $id);

        return Response::json(['message' => 'Removed from watch history']);
    }
}
