# AuthContext (frontend)

**Path:** [frontend/src/context/AuthContext.tsx](../../frontend/src/context/AuthContext.tsx)
**Layer:** React Context
**Consumed by:** [App](../../frontend/src/App.tsx), [Navbar](../../frontend/src/components/DashboardComponents/Navbar.tsx), [LoginPage](../../frontend/src/pages/LoginPage.tsx), [RegisterPage](../../frontend/src/pages/RegisterPage.tsx)

## Purpose
Holds the current auth token. Source of truth for whether the user is logged in. Persists to localStorage so refresh doesn't log them out.

## Public interface
- `token: string | null` — current token, null if logged out
- `login(token): void` — sets token in state + localStorage
- `logout(): void` — clears both

## Key invariants
- **`token` in state mirrors `localStorage.getItem('token')` exactly.** Never update one without the other.
- **No token validation here.** The frontend assumes any non-null token is valid; invalid tokens manifest as 401 responses from the API, which logout must then be triggered from.

## Gotchas
- Currently no 401-response handler auto-logs-out. If a token is revoked server-side, the frontend keeps sending it and getting 401s until the user manually logs out. Future: intercept in [connection.ts](../../frontend/src/api/connection.ts).
- `useAuth()` returns `AuthContextType | null`. Consumers must null-check. Could be tightened by throwing from the hook like [WatchedContext](WatchedContext.md) does.
