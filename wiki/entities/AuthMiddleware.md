# AuthMiddleware

**Path:** [backend/Middleware/AuthMiddleware.php](../../backend/Middleware/AuthMiddleware.php)
**Layer:** Middleware
**Implements:** [MiddlewareInterface](../../backend/Middleware/MiddlewareInterface.php)
**Depends on:** [Database](Database.md)

## Purpose
Validates `Authorization: Bearer <token>` header against the `api_tokens` table. On success, attaches `userId` to the [Request](Request.md). On failure, responds 401 and halts.

## Key invariants
- **Token must be prefixed with `Bearer `.** Raw token without prefix → 401.
- **Token is looked up verbatim** — no hashing. The token IS the credential. If the DB leaks, all tokens are compromised.
- **`request->setUserId(...)` is the only side effect** on success — actions then read it via `$request->getUserId()`.

## Order matters
In [public/index.php](../../public/index.php), AuthMiddleware must come *after* [CorsMiddleware](CorsMiddleware.md) in route definitions. CORS headers need to be set even on 401 responses, or browsers reject the error.

## Gotchas
- No token expiry or rotation currently. A leaked token is valid forever.
- No rate limiting on auth — the `/login` endpoint is open to brute force. Future work.
