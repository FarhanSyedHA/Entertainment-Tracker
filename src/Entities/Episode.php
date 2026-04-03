<?php

namespace App\Entities;

class Episode
{
    public int $id;
    public int $seasonId;
    public int $episodeNumber;
    public ?string $name;
    public ?string $overview;
    public ?string $stillUrl;
    public ?string $airDate;
    public ?int $runtimeMinutes;

    public static function fromRow(array $row): self
    {
        $e = new self();
        $e->id = (int) $row['id'];
        $e->seasonId = (int) $row['season_id'];
        $e->episodeNumber = (int) $row['episode_number'];
        $e->name = $row['name'];
        $e->overview = $row['overview'];
        $e->stillUrl = $row['still_url'];
        $e->airDate = $row['air_date'];
        $e->runtimeMinutes = $row['runtime_minutes'] ? (int) $row['runtime_minutes'] : null;
        return $e;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'season_id' => $this->seasonId,
            'episode_number' => $this->episodeNumber,
            'name' => $this->name,
            'overview' => $this->overview,
            'still_url' => $this->stillUrl,
            'air_date' => $this->airDate,
            'runtime_minutes' => $this->runtimeMinutes,
        ];
    }
}
