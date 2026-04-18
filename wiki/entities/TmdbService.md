# TmdbService

**Path:** [backend/Services/Api/TmdbService.php](../../backend/Services/Api/TmdbService.php)
**Layer:** Domain / External API client
**Implements:** [MoviesSourceInterface](../../backend/Interfaces/MoviesSourceInterface.php), [TvShowsSourceInterface](../../backend/Interfaces/TvShowsSourceInterface.php)
**Depends on:** [HttpClient](HttpClient.md), [TmdbAdapter](TmdbAdapter.md)

## Purpose
Wraps The Movie Database REST API for movies + TV shows. All outbound calls go through [HttpClient](HttpClient.md); all response shaping delegates to [TmdbAdapter](TmdbAdapter.md).

## Public interface
- `getTrendingMovies()`, `getTrendingTvShows()` — weekly trending lists
- `getMovieDetails($id)`, `getTvShowDetails($id)` — single-item details with genres, runtime
- `searchMovies($q)`, `searchTvShows($q)` — search endpoints
- `getTvShowSeasons($id)` — returns raw TMDB `seasons[]` array for adapter consumption
- `getSeasonEpisodes($tvId, $seasonNumber)` — returns raw `episodes[]` for a specific season

## Key invariants
- **API key comes from env** (`TMDB_API_KEY`). Never commit. See [.env.development](../../.env.development) for dev key.
- **Raw methods return raw TMDB JSON; shaped methods go through the adapter.** `get*Seasons` and `getSeasonEpisodes` are raw because [ContentService](ContentService.md) needs both shapes (raw for adaptation, shaped for response).

## Why this class implements two interfaces
TMDB genuinely provides both movies and TV shows — one API key, one base URL, one HTTP plumbing. Splitting into two classes would duplicate the credentials and config. See [content-source-interfaces](../decisions/content-source-interfaces.md).

## Gotchas
- TMDB poster paths are relative (`/abc.jpg`) — the adapter prepends `https://image.tmdb.org/t/p/w500`. Don't change that URL without updating the adapter.
- Season 0 (Specials) appears in `getTvShowSeasons` output. The adapter filters it out; don't remove that filter.
