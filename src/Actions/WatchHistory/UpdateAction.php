<?php

namespace App\Actions\WatchHistory;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Services\WatchHistoryService;

class UpdateAction
{
    public function __invoke(Request $request): Response
    {
        $id = (int) $request->getParam('id');
        $body = $request->getBody();

        $service = new WatchHistoryService();

        if (isset($body['status'])) {
            $entry = $service->updateStatus($request->user->id, $id, $body['status']);
        } elseif (isset($body['progress_percent'])) {
            $entry = $service->updateProgress(
                $request->user->id,
                $id,
                (int) $body['progress_percent'],
                isset($body['last_position_seconds']) ? (int) $body['last_position_seconds'] : null,
                isset($body['duration_seconds']) ? (int) $body['duration_seconds'] : null
            );
        } else {
            throw new ValidationException(['Provide status or progress_percent']);
        }

        return Response::json(['watch_history' => $entry->toArray()]);
    }
}
