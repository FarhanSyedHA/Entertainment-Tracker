<?php
namespace App\Services\Api;

use App\Adapters\TmdbAdapter;
use App\Interfaces\MoviesSourceInterface;
use App\Interfaces\TvShowsSourceInterface;

class TmdbService implements MoviesSourceInterface,TvShowsSourceInterface
{
  private string $baseTmdbUrl;
  private string $tmdbApiKey;

  public function __construct() 
  {
    $this->baseTmdbUrl = 'https://api.themoviedb.org/3';
    $this->tmdbApiKey = getenv('TMDB_API_KEY');
  }

  public function getTrendingMovies(): array
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/trending/movie/week?api_key=' . $this->tmdbApiKey); 
    return TmdbAdapter::toShows($raw, 'movie');
  }
  
  public function getTrendingTvShows(): array
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/trending/tv/week?api_key=' . $this->tmdbApiKey);
    return TmdbAdapter::toShows($raw, 'tvshows');
  }

  public function getMovieDetails(int $id): array
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/movie/' . $id . '?api_key=' . $this->tmdbApiKey);
    return TmdbAdapter::toDetails($raw, 'movie');
  }

  public function getTvShowDetails(int $id): array
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/tv/' . $id . '?api_key=' . $this->tmdbApiKey);
    return TmdbAdapter::toDetails($raw, 'tvshows');
  }

  public function getTvShowSeasons(int $id): array
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/tv/' . $id . '?api_key=' . $this->tmdbApiKey);
    return $raw['seasons'] ?? [];
  }

  public function getSeasonEpisodes(int $tvId, int $seasonNumber): array
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/tv/' . $tvId . '/season/' . $seasonNumber . '?api_key=' . $this->tmdbApiKey);
    return $raw['episodes'] ?? [];
  }

  public function searchMovies(string $searchQuery): array
  {
    $searchQuery = urlencode($searchQuery);
    $raw = HttpClient::get($this->baseTmdbUrl . '/search/movie?query=' . $searchQuery . '&include_adult=true&api_key=' . $this->tmdbApiKey);
    return TmdbAdapter::toShows($raw, 'movie');
  }

  public function searchTvShows(string $searchQuery): array
  {
    $searchQuery = urlencode($searchQuery);
    $raw = HttpClient::get($this->baseTmdbUrl . '/search/tv?query=' . $searchQuery . '&include_adult=false&api_key=' . $this->tmdbApiKey);
    return TmdbAdapter::toShows($raw, 'tvshows');
  }
}