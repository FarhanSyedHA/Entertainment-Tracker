<?php
namespace App\Services\Api;

use App\Adapters\TmdbAdapter;

class TmdbService
{
  private string $baseTmdbUrl;
  private string $tmdbApiKey;

  public function __construct() 
  {
    $this->baseTmdbUrl = 'https://api.themoviedb.org/3';
    $this->tmdbApiKey = getenv('TMDB_API_KEY');
  }

  public function getTrendingMovies()
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/trending/movie/week?api_key=' . $this->tmdbApiKey); 
    return TmdbAdapter::toShows($raw, 'movie');
  }
  
  public function getTrendingTvShows()
  {
    $raw = HttpClient::get($this->baseTmdbUrl . '/trending/tv/week?api_key=' . $this->tmdbApiKey); 
    return TmdbAdapter::toShows($raw, 'tvshows');
  }

}