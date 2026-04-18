# ContentService

**Path:** [backend/Services/ContentService.php](../../backend/Services/ContentService.php)
**Layer:** Domain / Service
**Depends on:** [Database](Database.md), [TmdbService](TmdbService.md), [JikanService](JikanService.md), [TmdbAdapter](TmdbAdapter.md), [JikanAdapter](JikanAdapter.md)
**Used by:** [ContentDetailsAction](../flows/modal-open.md)

## Purpose
Cache-through layer for show details + seasons + episodes. On first modal open for a given show, fetches from TMDB/Jikan and persists season/episode metadata into the `season` and `episode` tables. Subsequent opens read from DB, no external API calls.

## Public interface
- `getDetailsWithSeasons(int $externalId, string $frontendType): array` — entry point used by [ContentDetailsAction](../../backend/Actions/Content/ContentDetailsAction.php). `$frontendType` is `'movie' | 'tvshows' | 'anime'`. Returns the full details shape (title, poster, overview, genres, runtime) plus a `seasons[]` array.

## Internal flow (tvshows/anime)
1. Fetch details from external API (TMDB or Jikan) via the service.
2. `findOrCreateContent` — look up by `(type, tmdb_id|jikan_id)`. Insert if not cached.
3. `loadCachedSeasons` — if `season` rows exist for this `content_id`, load them + episodes.
4. On cache miss → `fetchAndCacheSeasons` inside a transaction:
   - TV: iterate TMDB seasons, fetch episodes per season, skip Season 0 (specials).
   - Anime: paginate Jikan episodes, wrap in a single synthetic "Season 1" row.
   - Persist all rows with `INSERT IGNORE` so re-runs are idempotent.

## Key invariants
- **Movies bypass season logic entirely.** `seasons: []` always for movies.
- **Anime always has exactly one `season` row** with `season_number = 1` — see [anime-seasons-model](../decisions/anime-seasons-model.md).
- **Transaction rollback on any failure** during season/episode fetch — we never half-populate the cache. If Jikan 504s mid-pagination, the whole batch is discarded.

## Gotchas
- Jikan rate limits + long anime (1000+ episodes) cause multi-second response times on first open. The 350ms sleep between pagination calls is deliberate — stay under Jikan's 3 req/sec limit.
- TMDB Season 0 (Specials) is intentionally filtered in [TmdbAdapter::toSeasonSummaries](../../backend/Adapters/TmdbAdapter.php). Do not change without updating [episode-numbering](../decisions/anime-seasons-model.md).
