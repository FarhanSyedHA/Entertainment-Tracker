<?php

namespace App\Entities;

class Content
{
    public int $id;
    public string $contentType;
    public ?int $tmdbId;
    public ?int $jikanId;
    public ?string $imdbId;
    public string $title;
    public ?string $originalTitle;
    public ?string $overview;
    public ?string $posterUrl;
    public ?string $backdropUrl;
    public ?string $releaseDate;
    public ?string $endDate;
    public ?int $runtimeMinutes;
    public ?string $status;
    public ?float $ratingTmdb;
    public ?float $ratingImdb;
    public ?string $ratingRt;
    public ?int $ratingMetacritic;
    public ?array $genres;
    public ?int $totalSeasons;
    public ?int $totalEpisodes;
    public ?string $metadataUpdatedAt;
    public string $createdAt;

    public static function fromRow(array $row): self
    {
        $c = new self();
        $c->id = (int) $row['id'];
        $c->contentType = $row['content_type'];
        $c->tmdbId = $row['tmdb_id'] ? (int) $row['tmdb_id'] : null;
        $c->jikanId = $row['jikan_id'] ? (int) $row['jikan_id'] : null;
        $c->imdbId = $row['imdb_id'];
        $c->title = $row['title'];
        $c->originalTitle = $row['original_title'];
        $c->overview = $row['overview'];
        $c->posterUrl = $row['poster_url'];
        $c->backdropUrl = $row['backdrop_url'];
        $c->releaseDate = $row['release_date'];
        $c->endDate = $row['end_date'];
        $c->runtimeMinutes = $row['runtime_minutes'] ? (int) $row['runtime_minutes'] : null;
        $c->status = $row['status'];
        $c->ratingTmdb = $row['rating_tmdb'] ? (float) $row['rating_tmdb'] : null;
        $c->ratingImdb = $row['rating_imdb'] ? (float) $row['rating_imdb'] : null;
        $c->ratingRt = $row['rating_rt'];
        $c->ratingMetacritic = $row['rating_metacritic'] ? (int) $row['rating_metacritic'] : null;
        $c->genres = $row['genres'] ? json_decode($row['genres'], true) : null;
        $c->totalSeasons = $row['total_seasons'] ? (int) $row['total_seasons'] : null;
        $c->totalEpisodes = $row['total_episodes'] ? (int) $row['total_episodes'] : null;
        $c->metadataUpdatedAt = $row['metadata_updated_at'];
        $c->createdAt = $row['created_at'];
        return $c;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content_type' => $this->contentType,
            'tmdb_id' => $this->tmdbId,
            'jikan_id' => $this->jikanId,
            'imdb_id' => $this->imdbId,
            'title' => $this->title,
            'original_title' => $this->originalTitle,
            'overview' => $this->overview,
            'poster_url' => $this->posterUrl,
            'backdrop_url' => $this->backdropUrl,
            'release_date' => $this->releaseDate,
            'end_date' => $this->endDate,
            'runtime_minutes' => $this->runtimeMinutes,
            'status' => $this->status,
            'rating_tmdb' => $this->ratingTmdb,
            'rating_imdb' => $this->ratingImdb,
            'rating_rt' => $this->ratingRt,
            'rating_metacritic' => $this->ratingMetacritic,
            'genres' => $this->genres,
            'total_seasons' => $this->totalSeasons,
            'total_episodes' => $this->totalEpisodes,
        ];
    }
}
