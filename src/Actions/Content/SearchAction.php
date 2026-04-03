<?php

namespace App\Actions\Content;

use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\ValidationException;
use App\Services\ContentService;

class SearchAction
{
    public function __invoke(Request $request): Response
    {
        $query = $request->getQuery('q', '');
        $type = $request->getQuery('type', 'all');

        if (empty(trim($query))) {
            throw new ValidationException(['Search query is required']);
        }

        if (!in_array($type, ['all', 'movie', 'tv', 'anime'])) {
            throw new ValidationException(['Invalid type filter']);
        }

        $service = new ContentService();
        $results = $service->search($query, $type);

        return Response::json(['results' => $results]);
    }
}
