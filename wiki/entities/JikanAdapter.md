# JikanAdapter

**Path:** [backend/Adapters/JikanAdapter.php](../../backend/Adapters/JikanAdapter.php)
**Layer:** Domain / Adapter (pure static)
**Used by:** [JikanService](JikanService.md), [ContentService](ContentService.md)

## Purpose
Shape raw Jikan JSON into the project's internal `Show` / `ShowDetails` / `Episode` shapes. Pure, no I/O, no state.

## Public interface
- `toShows(array $raw)` — trending/search → `Show[]`, with deduplication
- `toDetails(array $raw)` — single-item → `ShowDetails`, includes `total_episodes`
- `toEpisodes(array $rawEpisodes)` — aggregated paginated episodes → internal episode shape

## Key invariants
- **Deduplication lives in `toShows`.** Jikan returns duplicate anime entries in search responses — the adapter filters them via `$seen[$s['id']]`. Don't move this logic elsewhere without guarding callers.
- **`episode_number` sourced from `mal_id`**, not array index. Jikan's episode objects use `mal_id` for the episode number field.
- **No runtime per episode.** Jikan doesn't expose per-episode duration — `runtime_seconds` is always null in the episode shape.

## Gotchas
- Year comes from `aired.from` (ISO date) — substring `0-4` gives the year. Empty string if missing; don't assume numeric.
- Poster prefers `large_image_url`, falls back to `image_url`. Always JPG in Jikan.
- No backdrops — Jikan uses trailer thumbnail as a poor substitute. Anime modals often render without a backdrop.
