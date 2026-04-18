# WatchService

**Path:** [backend/Services/WatchService.php](../../backend/Services/WatchService.php)
**Layer:** Domain / Service (static)
**Depends on:** [Database](Database.md)
**Used by:** [WatchStatusAction](../../backend/Actions/WatchHistory/WatchStatusAction.php)

## Purpose
Batch-resolves watched status for a list of external show IDs, keyed by the canonical `"source:externalId:type"` string used by the frontend.

## Public interface
- `getWatchedMap(int $userId, array $items): array` — items shape `[{source, externalId, type}, ...]`. Returns `{"tmdb:1234:movie": true, "jikan:42:anime": true, ...}`.

## Internal flow
1. Split items by source into `$tmdbIds` and `$jikanIds`.
2. One batch SQL per source: `JOIN content + progress WHERE content.tmdb_id IN (...) AND progress.user_id = ? AND status = 'completed'`.
3. Build result dictionary with canonical keys.

## Why static, not instance
No state. Called once per page load, short-lived. Avoids the ceremony of `new WatchService()` in every caller. This is deliberate; don't add instance state here.

## Key invariants
- **Keys must match frontend convention** — [WatchedContext.tsx](../../frontend/src/context/WatchedContext.tsx) builds the same key on its side. If you change the delimiter or ordering here, change it there.
- **Only status='completed' counts** as watched. In-progress rows don't set the badge.

## Gotchas
- Two separate queries (tmdb + jikan) because `content.tmdb_id IS NULL` for anime rows. Union-ing into one query would mean more coalescing in SQL. Two small queries read cleaner.
