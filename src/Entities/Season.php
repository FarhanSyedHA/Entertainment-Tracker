<?php

namespace App\Entities;

class Season
{
    public int $id;
    public int $contentId;
    public int $seasonNumber;
    public ?string $name;
    public ?string $overview;
    public ?string $posterUrl;
    public ?string $airDate;
    public ?int $episodeCount;

    public static function fromRow(array $row): self
    {
        $s = new self();
        $s->id = (int) $row['id'];
        $s->contentId = (int) $row['content_id'];
        $s->seasonNumber = (int) $row['season_number'];
        $s->name = $row['name'];
        $s->overview = $row['overview'];
        $s->posterUrl = $row['poster_url'];
        $s->airDate = $row['air_date'];
        $s->episodeCount = $row['episode_count'] ? (int) $row['episode_count'] : null;
        return $s;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content_id' => $this->contentId,
            'season_number' => $this->seasonNumber,
            'name' => $this->name,
            'overview' => $this->overview,
            'poster_url' => $this->posterUrl,
            'air_date' => $this->airDate,
            'episode_count' => $this->episodeCount,
        ];
    }
}
