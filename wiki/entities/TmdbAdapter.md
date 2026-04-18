# TmdbAdapter

**Path:** [backend/Adapters/TmdbAdapter.php](../../backend/Adapters/TmdbAdapter.php)
**Layer:** Domain / Adapter (pure static)
**Used by:** [TmdbService](TmdbService.md), [ContentService](ContentService.md)

## Purpose
Shape raw TMDB JSON into the project's internal `Show` / `ShowDetails` / `Season` / `Episode` shapes. Pure, no I/O, no state.

## Public interface
- `toShows(array $raw, string $type)` — trending/search lists → `Show[]`
- `toDetails(array $raw, string $type)` — single-item details → `ShowDetails`
- `toSeasonSummaries(array $rawSeasons)` — `seasons[]` from `/tv/{id}` → internal season summary shape (filters Season 0)
- `toEpisodes(array $rawEpisodes)` — `episodes[]` from `/tv/{id}/season/{n}` → internal episode shape

## Key invariants
- **Poster URLs get prepended** with `https://image.tmdb.org/t/p/w500`. Backdrops use `/original`.
- **Season 0 (specials) filtered** in `toSeasonSummaries` via `season_number >= 1`. Don't remove without reviewing [anime-seasons-model](../decisions/anime-seasons-model.md).
- **Runtime fallback** — movies use `$raw['runtime']`, TV uses first `episode_run_time` element. Nullable if missing.
- **Type param is the frontend type** (`'movie' | 'tvshows'`), not the TMDB type. The adapter passes it through to the output shape.

## Gotchas
- `$item['name']` vs `$item['title']` — TMDB uses `title` for movies, `name` for TV. Adapter coalesces via `??`. Don't assume one or the other.
- Ratings come through as 0 if missing — downstream code should handle "no rating" as 0.0, not null.
