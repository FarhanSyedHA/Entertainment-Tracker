<?php

namespace App\Actions\WatchHistory;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class StatsAction
{
    public function __invoke(Request $request): Response
    {
        $db = Database::getInstance();
        $userId = $request->user->id;

        // Total counts by type
        $stmt = $db->prepare('
            SELECT c.content_type, COUNT(DISTINCT wh.content_id) as count
            FROM watch_history wh
            INNER JOIN content c ON c.id = wh.content_id
            WHERE wh.user_id = :user_id
            GROUP BY c.content_type
        ');
        $stmt->execute(['user_id' => $userId]);
        $typeCounts = [];
        foreach ($stmt->fetchAll() as $row) {
            $typeCounts[$row['content_type']] = (int) $row['count'];
        }

        // Status counts
        $stmt = $db->prepare('
            SELECT wh.status, COUNT(DISTINCT wh.content_id) as count
            FROM watch_history wh
            WHERE wh.user_id = :user_id
            GROUP BY wh.status
        ');
        $stmt->execute(['user_id' => $userId]);
        $statusCounts = [];
        foreach ($stmt->fetchAll() as $row) {
            $statusCounts[$row['status']] = (int) $row['count'];
        }

        // Total episodes watched
        $stmt = $db->prepare('
            SELECT COUNT(*) as count FROM watch_history
            WHERE user_id = :user_id AND episode_id IS NOT NULL AND status = :status
        ');
        $stmt->execute(['user_id' => $userId, 'status' => 'completed']);
        $episodesWatched = (int) $stmt->fetch()['count'];

        // Total watch time (estimated from completed movies runtime)
        $stmt = $db->prepare('
            SELECT COALESCE(SUM(c.runtime_minutes), 0) as total
            FROM watch_history wh
            INNER JOIN content c ON c.id = wh.content_id
            WHERE wh.user_id = :user_id AND wh.status = :status AND c.content_type = :type
        ');
        $stmt->execute(['user_id' => $userId, 'status' => 'completed', 'type' => 'movie']);
        $movieMinutes = (int) $stmt->fetch()['total'];

        return Response::json([
            'stats' => [
                'by_type' => $typeCounts,
                'by_status' => $statusCounts,
                'episodes_watched' => $episodesWatched,
                'movie_watch_time_minutes' => $movieMinutes,
                'movie_watch_time_hours' => round($movieMinutes / 60, 1),
            ],
        ]);
    }
}
