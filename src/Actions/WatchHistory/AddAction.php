<?php

namespace App\Actions\WatchHistory;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Services\WatchHistoryService;

class AddAction
{
    public function __invoke(Request $request): Response
    {
        $body = $request->getBody();
        $contentId = (int) ($body['content_id'] ?? 0);
        $episodeId = !empty($body['episode_id']) ? (int) $body['episode_id'] : null;
        $status = $body['status'] ?? 'in_progress';

        if ($contentId <= 0) {
            throw new ValidationException(['content_id is required']);
        }

        if (!in_array($status, ['in_progress', 'completed', 'dropped'])) {
            throw new ValidationException(['Invalid status']);
        }

        $service = new WatchHistoryService();
        $entry = $service->add($request->user->id, $contentId, $episodeId, $status);

        return Response::json(['watch_history' => $entry->toArray()], 201);
    }
}
