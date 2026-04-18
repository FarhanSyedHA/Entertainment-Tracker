# Response

**Path:** [backend/Core/Response.php](../../backend/Core/Response.php)
**Layer:** Core
**Used by:** All middleware and actions

## Purpose
Static helpers for emitting JSON responses + standard HTTP errors. Every helper calls `exit` after writing — so responses halt execution.

## Public interface
- `json(array $data, int $status = 200): void` — generic JSON response, exits
- `unauthorized($msg = 'Unauthorized'): void` — 401
- `notFound($msg = 'Not found'): void` — 404
- `badRequest($msg = 'Bad request'): void` — 400
- `success($msg = 'success'): void` — 200 with `{success: msg}` shape

## Key invariants
- **Every method exits.** Never returns. Think of these as `throw` equivalents that write HTTP responses instead of propagating exceptions.
- **Error shape is always `{error: "message"}`.** Frontend `callAPI` wrapper checks for `data.error` and throws. See [connection.ts](../../frontend/src/api/connection.ts).
- **Success shape varies.** Some actions return their own shape (`{data: ..., watched: ...}`), others use `success()`. This is intentional — `success()` is only for ops with no meaningful return.

## Gotchas
- `exit` means middleware after a Response call does not run. Ordering matters — CORS must set headers *before* auth may 401.
- No content negotiation. All responses are JSON. Add `Accept` header handling if HTML ever becomes a requirement.
