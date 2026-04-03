<?php

namespace App\Actions\Content;

use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\NotFoundException;
use App\Services\ContentService;

class GetAction
{
    public function __invoke(Request $request): Response
    {
        $id = (int) $request->getParam('id');

        $service = new ContentService();
        $content = $service->getById($id);

        if (!$content) {
            throw new NotFoundException('Content not found');
        }

        $data = $content->toArray();

        // Include seasons/episodes for TV and anime
        if (in_array($content->contentType, ['tv', 'anime'])) {
            $seasons = $service->getSeasons($content->id);
            $data['seasons'] = array_map(function ($season) use ($service) {
                $seasonArr = $season->toArray();
                $episodes = $service->getEpisodes($season->id);
                $seasonArr['episodes'] = array_map(fn($ep) => $ep->toArray(), $episodes);
                return $seasonArr;
            }, $seasons);
        }

        return Response::json(['content' => $data]);
    }
}
