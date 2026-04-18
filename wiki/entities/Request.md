# Request

**Path:** [backend/Core/Request.php](../../backend/Core/Request.php)
**Layer:** Core
**Used by:** All middleware and actions

## Purpose
Thin wrapper around PHP superglobals (`$_SERVER`, `$_GET`, stdin). Gives the rest of the app a typed, testable surface instead of scattered superglobal reads.

## Public interface
- `getMethod()`, `getPath()` — URL parts (path strips query string)
- `getBody()` — JSON-decoded request body (null if empty or non-JSON)
- `getQuery($name)` — query string param
- `getHeader($name)` — HTTP header (case-insensitive, converts hyphens to underscores)
- `getUserId()`, `setUserId($id)` — mutable slot filled by [AuthMiddleware](AuthMiddleware.md)

## Key invariants
- **Constructor reads body once.** `php://input` is a one-shot stream; re-reading returns empty. Request object caches it.
- **`setUserId` is only called by [AuthMiddleware](AuthMiddleware.md).** Actions should treat `getUserId()` as authoritative — null means "not authenticated".

## Gotchas
- `getHeader('Content-Type')` works; `getHeader('X-Custom-Header')` works. But PHP strips some headers (Authorization on some hosts) — `HTTP_AUTHORIZATION` may not appear in `$_SERVER` without specific server config.
- JSON decode failure returns null, not throws. Actions that require a body must check `empty($body)` explicitly.
