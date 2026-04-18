# JikanService

**Path:** [backend/Services/Api/JikanService.php](../../backend/Services/Api/JikanService.php)
**Layer:** Domain / External API client
**Implements:** [AnimeSourceInterface](../../backend/Interfaces/AnimeSourceInterface.php)
**Depends on:** [HttpClient](HttpClient.md), [JikanAdapter](JikanAdapter.md)

## Purpose
Wraps Jikan (unofficial MyAnimeList REST API) for anime content. Single source of anime data.

## Public interface
- `getTrendingAnime()` — `/top/anime?filter=airing`
- `getAnimeDetails($id)` — `/anime/{id}/full`, single-item
- `searchAnime($q)` — `/anime?q=...&sfw=true&limit=25`
- `getAnimeEpisodes($id)` — paginated loop over `/anime/{id}/episodes`, aggregates all pages (100/page), sleeps 350ms between calls

## Key invariants
- **No API key required.** Jikan is public + rate-limited.
- **Rate limit: ~3 req/sec.** The 350ms sleep between pagination pages stays comfortably under that.
- **Pagination cap: 20 pages = 2000 episodes.** Enough for even One Piece. If we ever exceed this the cap can be raised.
- **Error responses come back as arrays with `error` keys** (via [HttpClient](HttpClient.md)), not thrown exceptions. Callers must check for empty `data`.

## Gotchas
- Jikan returns duplicate anime in search results — deduplication lives in [JikanAdapter::toShows](../../backend/Adapters/JikanAdapter.php). Don't move it; other callers aren't guarded.
- Jikan has occasional 504 outages (service-wide). [HttpClient](HttpClient.md)'s `@file_get_contents` suppressor prevents PHP warnings from leaking into the JSON response body.
- Anime has no native season model on MyAnimeList — see [anime-seasons-model](../decisions/anime-seasons-model.md).
