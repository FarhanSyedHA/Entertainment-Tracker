# AuthService

**Path:** [backend/Services/AuthService.php](../../backend/Services/AuthService.php)
**Layer:** Domain / Service
**Depends on:** [Database](Database.md)
**Used by:** [RegisterAction](../../backend/Actions/Auth/RegisterAction.php), [LoginAction](../../backend/Actions/Auth/LoginAction.php)

## Purpose
Owns the credential lifecycle — register, login, token issuance. Does not handle token validation at request time (that's [AuthMiddleware](AuthMiddleware.md)).

## Public interface
- `register($email, $username, $password): array` — rejects duplicates, bcrypts password, inserts user row, issues token. Returns `['token' => ..., 'userId' => ...]` or `['error' => ...]`.
- `login($email, $password): array` — verifies bcrypt hash, issues a new token on success. Returns same shape as register.

## Key invariants
- **Passwords are always bcrypt-hashed** via `password_hash(..., PASSWORD_BCRYPT)` before storage. Never touch the `password` column directly.
- **Every login issues a fresh token.** Old tokens remain valid (we don't delete on new login). This is intentional for multi-device use but means there's no "logout everywhere" without truncating the row.
- **Tokens are 32 random bytes hex-encoded** (64-char string). No JWT — stored server-side in `api_tokens` table.

## Gotchas
- Token is created *before* the method returns — there is no "create user without token" path. If we ever need email verification, this needs restructuring.
- Returning errors as `['error' => ...]` instead of throwing is a legacy choice. The Actions check the array key and set HTTP status. Future: switch to exceptions.
