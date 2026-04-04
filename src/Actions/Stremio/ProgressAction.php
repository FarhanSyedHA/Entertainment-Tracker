<?php

namespace App\Actions\Stremio;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\HttpException;
use App\Services\WatchHistoryService;

class ProgressAction
{
    public function __invoke(Request $request): Response
    {
        $body = $request->getBody();
        $imdbId = $body['imdb_id'] ?? null;
        $seasonNum = $body['season'] ?? null;
        $episodeNum = $body['episode'] ?? null;
        $currentTime = $body['current_time'] ?? null;
        $duration = $body['duration'] ?? null;

        if (!$imdbId || $currentTime === null || $duration === null) {
            throw new HttpException('imdb_id, current_time, and duration are required', 422);
        }

        $duration = (int) $duration;
        $currentTime = (int) $currentTime;

        if ($duration <= 0) {
            throw new HttpException('Invalid duration', 422);
        }

        $progressPercent = min(100, (int) round(($currentTime / $duration) * 100));

        $db = Database::getInstance();

        // Find content
        $stmt = $db->prepare('SELECT id FROM content WHERE imdb_id = :imdb_id');
        $stmt->execute(['imdb_id' => $imdbId]);
        $contentRow = $stmt->fetch();

        if (!$contentRow) {
            throw new HttpException('Content not found. Send webhook first.', 404);
        }

        $contentId = (int) $contentRow['id'];

        // Find episode if applicable
        $episodeId = null;
        if ($seasonNum && $episodeNum) {
            $stmt = $db->prepare('
                SELECT e.id FROM episodes e
                INNER JOIN seasons s ON s.id = e.season_id
                WHERE s.content_id = :content_id
                AND s.season_number = :season_num
                AND e.episode_number = :episode_num
            ');
            $stmt->execute([
                'content_id' => $contentId,
                'season_num' => $seasonNum,
                'episode_num' => $episodeNum,
            ]);
            $epRow = $stmt->fetch();
            $episodeId = $epRow ? (int) $epRow['id'] : null;
        }

        // Find the watch history entry
        $stmt = $db->prepare('
            SELECT id FROM watch_history
            WHERE user_id = :user_id AND content_id = :content_id
            AND (episode_id = :episode_id OR (episode_id IS NULL AND :episode_id2 IS NULL))
        ');
        $stmt->execute([
            'user_id' => $request->user->id,
            'content_id' => $contentId,
            'episode_id' => $episodeId,
            'episode_id2' => $episodeId,
        ]);
        $whRow = $stmt->fetch();

        if (!$whRow) {
            throw new HttpException('No watch history entry found. Send webhook first.', 404);
        }

        $whService = new WatchHistoryService();
        $entry = $whService->updateProgress(
            $request->user->id,
            (int) $whRow['id'],
            $progressPercent,
            $currentTime,
            $duration
        );

        return Response::json([
            'message' => 'Progress updated',
            'watch_history' => $entry->toArray(),
        ]);
    }
}
