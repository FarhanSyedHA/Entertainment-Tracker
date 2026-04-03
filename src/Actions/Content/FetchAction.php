<?php

namespace App\Actions\Content;

use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\ValidationException;
use App\Services\ContentService;

class FetchAction
{
    public function __invoke(Request $request): Response
    {
        $body = $request->getBody();
        $contentType = $body['content_type'] ?? '';
        $externalId = (int) ($body['external_id'] ?? 0);

        if (!in_array($contentType, ['movie', 'tv', 'anime'])) {
            throw new ValidationException(['Invalid content_type']);
        }

        if ($externalId <= 0) {
            throw new ValidationException(['Invalid external_id']);
        }

        $service = new ContentService();
        $content = $service->fetchAndCache($contentType, $externalId);

        return Response::json(['content' => $content->toArray()], 201);
    }
}
