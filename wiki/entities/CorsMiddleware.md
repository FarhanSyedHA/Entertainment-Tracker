# CorsMiddleware

**Path:** [backend/Middleware/CorsMiddleware.php](../../backend/Middleware/CorsMiddleware.php)
**Layer:** Middleware
**Implements:** [MiddlewareInterface](../../backend/Middleware/MiddlewareInterface.php)

## Purpose
Sets `Access-Control-Allow-*` headers so the browser accepts cross-origin requests from the frontend.

## Key invariants
- **Reads `CORS_ORIGIN` env var.** Defaults to `*` if unset — dev convenience, but `*` + `Allow-Credentials: true` is non-standard and some browsers reject it.
- **Applied to every route via middleware chain.** There's also a duplicate block in [Router::resolve](../../backend/Core/Router.php) for OPTIONS preflights, which bypasses the middleware chain entirely.
- **Credentials allowed** — needed because the frontend sends `Authorization: Bearer` headers.

## Gotchas
- The duplicate CORS logic in Router is a mild DRY violation. It exists because OPTIONS preflights shouldn't hit route handlers, but also need CORS headers. Unifying would require a middleware-for-OPTIONS pattern that's overkill for one endpoint type.
- Production `CORS_ORIGIN` should be the exact Vercel URL, not `*`. See [.env](../../.env).
