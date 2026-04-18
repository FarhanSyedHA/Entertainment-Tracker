# HttpClient

**Path:** [backend/Services/Api/HttpClient.php](../../backend/Services/Api/HttpClient.php)
**Layer:** Domain / Infrastructure
**Used by:** [TmdbService](TmdbService.md), [JikanService](JikanService.md)

## Purpose
Minimal static wrapper around `file_get_contents` with JSON decoding. Not a full HTTP client — just enough to GET external APIs.

## Public interface
- `static get(string $url): array` — returns decoded JSON as associative array, or `['error' => 'Request Failed']` on failure.

## Key invariants
- **Always returns an array**, never null or throws. Callers don't need try/catch.
- **10-second timeout** — requests longer than this get up and abandoned.
- **`@` suppressor on `file_get_contents`** is intentional — prevents PHP warnings from leaking into the response body when external APIs return HTTP errors. See [Known Issues in CLAUDE.md](../../CLAUDE.md) and the Jikan 504 incident.

## Why not cURL or Guzzle?
Intentionally minimal. `file_get_contents` + stream context is built-in, zero dependencies, readable in 20 lines. When we outgrow it (retries, auth headers, POST bodies), upgrade. Until then, YAGNI.

## Gotchas
- Does not parse non-JSON responses. If an external API returns HTML (e.g. a CDN error page), `json_decode` returns null and callers get a type error. Check for `null` return in services before array operations.
