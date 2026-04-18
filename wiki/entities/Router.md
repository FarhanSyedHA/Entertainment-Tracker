# Router

**Path:** [backend/Core/Router.php](../../backend/Core/Router.php)
**Layer:** Core
**Used by:** [public/index.php](../../public/index.php)

## Purpose
Simple path-based router with middleware chain. Matches `(method, path)` exactly — no route parameters, no regex.

## Public interface
- `addRoute($method, $path, $handler, $middleware = []): self` — chainable
- `resolve(Request $request): void` — dispatches or 404s

## Flow
1. Short-circuit OPTIONS → emit CORS headers + 200 + exit (bypasses middleware chain for perf).
2. Look up `$routes[$method][$path]`. 404 if missing.
3. For each middleware class → instantiate → `handle($request)`. Middleware can halt by calling `Response::*` which exits.
4. Instantiate action class → `handle($request)`.

## Key invariants
- **Exact path matching.** `/api/shows/123` would require either adding a regex layer or each ID as a query parameter. Current design uses query params (e.g. `/api/content-details?id=...&type=...`).
- **Middleware runs in array order.** Always `CorsMiddleware` before `AuthMiddleware` — CORS headers must accompany even 401 responses.
- **Handlers are instantiated per request** via `new $class()`. No DI container; actions with dependencies new them up themselves.

## Gotchas
- No HTTP method override or content-negotiation. Method is taken verbatim from `$_SERVER['REQUEST_METHOD']`.
- OPTIONS short-circuit means preflight does not run any middleware. Any auth on OPTIONS would need to move into the shortcut block.
