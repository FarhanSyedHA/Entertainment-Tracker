# WatchedContext (frontend)

**Path:** [frontend/src/context/WatchedContext.tsx](../../frontend/src/context/WatchedContext.tsx)
**Layer:** React Context
**Consumed by:** [ContentModal](ContentModal.md), [ShowsGrid](../../frontend/src/components/DashboardComponents/ShowsGrid.tsx), [Content](../../frontend/src/components/DashboardComponents/Content.tsx)

## Purpose
App-wide cache of which shows the current user has marked as watched. Keeps the "watched" badge in sync across the grid and the modal without per-component fetches.

## Public interface
- `isWatched(type, id): boolean` — read from local map
- `setWatched(type, id, watched): void` — optimistic local update (after a successful API call)
- `refreshFor(items): Promise<void>` — batch-fetches watched status for a list of shows, merges into local map

## Key invariants
- **Map key format: `"${source}:${id}:${backendType}"`** — e.g. `"tmdb:1244:movie"`, `"jikan:42:anime"`. Matches [WatchService](WatchService.md) output. If one changes, both must.
- **Source/type translation happens here.** Frontend uses `'tvshows'` + (tmdb implicit); backend uses `'tv'`. The `toBackendType` / `toSource` helpers do the mapping.
- **Provider throws if not wrapped.** `useWatched` errors explicitly — catches bugs where a component is rendered outside the provider tree.

## Gotchas
- `refreshFor` merges via `{...prev, ...res.watched}` — it does not remove stale keys. If a user unwatches outside the app, the local map stays true until a full refresh.
- Optimistic updates in [ContentModal](ContentModal.md) call `setWatched` *after* the API call succeeds, not before. Safer (no rollback needed) but slightly less snappy.
