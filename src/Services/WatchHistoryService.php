<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Exceptions\HttpException;
use App\Core\Exceptions\NotFoundException;
use App\Entities\WatchHistory;

class WatchHistoryService
{
    public function getList(int $userId, ?string $contentType = null): array
    {
        $db = Database::getInstance();

        $sql = '
            SELECT wh.*, c.content_type, c.title, c.poster_url, c.release_date,
                   c.rating_tmdb, c.tmdb_id, c.jikan_id
            FROM watch_history wh
            INNER JOIN content c ON c.id = wh.content_id
            WHERE wh.user_id = :user_id
        ';
        $params = ['user_id' => $userId];

        if ($contentType && $contentType !== 'all') {
            $sql .= ' AND c.content_type = :content_type';
            $params['content_type'] = $contentType;
        }

        // For TV/anime, get the latest watch per content (not per episode)
        $sql .= ' ORDER BY wh.last_watched_at DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Group by content_id — show one card per content, with aggregate progress
        $grouped = [];
        foreach ($rows as $row) {
            $cid = $row['content_id'];
            if (!isset($grouped[$cid])) {
                $grouped[$cid] = [
                    'id' => (int) $row['id'],
                    'content_id' => (int) $row['content_id'],
                    'content_type' => $row['content_type'],
                    'title' => $row['title'],
                    'poster_url' => $row['poster_url'],
                    'release_date' => $row['release_date'],
                    'rating_tmdb' => $row['rating_tmdb'] ? (float) $row['rating_tmdb'] : null,
                    'status' => $row['status'],
                    'progress_percent' => (int) $row['progress_percent'],
                    'watch_count' => (int) $row['watch_count'],
                    'last_watched_at' => $row['last_watched_at'],
                    'episode_id' => $row['episode_id'] ? (int) $row['episode_id'] : null,
                ];
            }
        }

        return array_values($grouped);
    }

    public function add(int $userId, int $contentId, ?int $episodeId = null, string $status = 'in_progress'): WatchHistory
    {
        $db = Database::getInstance();

        // Check content exists
        $stmt = $db->prepare('SELECT id FROM content WHERE id = :id');
        $stmt->execute(['id' => $contentId]);
        if (!$stmt->fetch()) {
            throw new NotFoundException('Content not found');
        }

        // Check if episode exists (if provided)
        if ($episodeId) {
            $stmt = $db->prepare('SELECT id FROM episodes WHERE id = :id');
            $stmt->execute(['id' => $episodeId]);
            if (!$stmt->fetch()) {
                throw new NotFoundException('Episode not found');
            }
        }

        // Check for existing entry
        $stmt = $db->prepare('
            SELECT id, watch_count FROM watch_history
            WHERE user_id = :user_id AND content_id = :content_id
            AND (episode_id = :episode_id OR (episode_id IS NULL AND :episode_id2 IS NULL))
        ');
        $stmt->execute([
            'user_id' => $userId,
            'content_id' => $contentId,
            'episode_id' => $episodeId,
            'episode_id2' => $episodeId,
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Rewatch — increment watch_count, update status
            $stmt = $db->prepare('
                UPDATE watch_history
                SET watch_count = watch_count + 1, status = :status,
                    last_watched_at = NOW(), progress_percent = 0
                WHERE id = :id
            ');
            $stmt->execute(['id' => $existing['id'], 'status' => $status]);
            return $this->getById((int) $existing['id']);
        }

        // New entry
        $stmt = $db->prepare('
            INSERT INTO watch_history (user_id, content_id, episode_id, status, last_watched_at)
            VALUES (:user_id, :content_id, :episode_id, :status, NOW())
        ');
        $stmt->execute([
            'user_id' => $userId,
            'content_id' => $contentId,
            'episode_id' => $episodeId,
            'status' => $status,
        ]);

        return $this->getById((int) $db->lastInsertId());
    }

    public function updateStatus(int $userId, int $watchHistoryId, string $status): WatchHistory
    {
        $db = Database::getInstance();

        if (!in_array($status, ['in_progress', 'completed', 'dropped'])) {
            throw new HttpException('Invalid status', 422);
        }

        $progressPercent = $status === 'completed' ? 100 : null;

        $sql = 'UPDATE watch_history SET status = :status, last_watched_at = NOW()';
        $params = ['status' => $status, 'user_id' => $userId, 'id' => $watchHistoryId];

        if ($progressPercent !== null) {
            $sql .= ', progress_percent = :progress';
            $params['progress'] = $progressPercent;
        }

        $sql .= ' WHERE id = :id AND user_id = :user_id';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() === 0) {
            throw new NotFoundException('Watch history entry not found');
        }

        return $this->getById($watchHistoryId);
    }

    public function updateProgress(int $userId, int $watchHistoryId, int $progressPercent, ?int $lastPositionSeconds = null, ?int $durationSeconds = null): WatchHistory
    {
        $db = Database::getInstance();

        $progressPercent = max(0, min(100, $progressPercent));
        $status = $progressPercent >= 80 ? 'completed' : 'in_progress';

        $stmt = $db->prepare('
            UPDATE watch_history
            SET progress_percent = :progress, status = :status,
                last_position_seconds = :position, duration_seconds = :duration,
                last_watched_at = NOW()
            WHERE id = :id AND user_id = :user_id
        ');
        $stmt->execute([
            'progress' => $progressPercent,
            'status' => $status,
            'position' => $lastPositionSeconds,
            'duration' => $durationSeconds,
            'id' => $watchHistoryId,
            'user_id' => $userId,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new NotFoundException('Watch history entry not found');
        }

        return $this->getById($watchHistoryId);
    }

    public function delete(int $userId, int $watchHistoryId): void
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('DELETE FROM watch_history WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $watchHistoryId, 'user_id' => $userId]);

        if ($stmt->rowCount() === 0) {
            throw new NotFoundException('Watch history entry not found');
        }
    }

    private function getById(int $id): WatchHistory
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM watch_history WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new NotFoundException('Watch history entry not found');
        }

        return WatchHistory::fromRow($row);
    }
}
