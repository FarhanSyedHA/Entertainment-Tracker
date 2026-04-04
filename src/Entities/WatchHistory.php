<?php

namespace App\Entities;

class WatchHistory
{
    public int $id;
    public int $userId;
    public int $contentId;
    public ?int $episodeId;
    public string $status;
    public int $progressPercent;
    public int $watchCount;
    public ?int $lastPositionSeconds;
    public ?int $durationSeconds;
    public string $lastWatchedAt;
    public string $createdAt;
    public string $updatedAt;

    public static function fromRow(array $row): self
    {
        $wh = new self();
        $wh->id = (int) $row['id'];
        $wh->userId = (int) $row['user_id'];
        $wh->contentId = (int) $row['content_id'];
        $wh->episodeId = $row['episode_id'] ? (int) $row['episode_id'] : null;
        $wh->status = $row['status'];
        $wh->progressPercent = (int) $row['progress_percent'];
        $wh->watchCount = (int) $row['watch_count'];
        $wh->lastPositionSeconds = $row['last_position_seconds'] ? (int) $row['last_position_seconds'] : null;
        $wh->durationSeconds = $row['duration_seconds'] ? (int) $row['duration_seconds'] : null;
        $wh->lastWatchedAt = $row['last_watched_at'];
        $wh->createdAt = $row['created_at'];
        $wh->updatedAt = $row['updated_at'];
        return $wh;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'content_id' => $this->contentId,
            'episode_id' => $this->episodeId,
            'status' => $this->status,
            'progress_percent' => $this->progressPercent,
            'watch_count' => $this->watchCount,
            'last_position_seconds' => $this->lastPositionSeconds,
            'duration_seconds' => $this->durationSeconds,
            'last_watched_at' => $this->lastWatchedAt,
        ];
    }
}
