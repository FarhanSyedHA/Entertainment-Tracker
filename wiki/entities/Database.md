# Database

**Path:** [backend/Core/Database.php](../../backend/Core/Database.php)
**Layer:** Core / Infrastructure
**Used by:** All services that touch MySQL

## Purpose
PDO connection singleton. Ensures one connection per request lifecycle, not one per query.

## Public interface
- `static getInstance(): self` — returns the singleton wrapper
- `getConnection(): \PDO` — returns the underlying PDO handle

## Key invariants
- **Private constructor** — instantiation only via `getInstance`. This is deliberate; do not add a public constructor.
- **ERRMODE_EXCEPTION** is always on — callers get thrown PDOExceptions on SQL errors, not silent false returns.
- **SSL enforced for TiDB Cloud.** When `$host` contains `tidbcloud.com` OR `APP_ENV=production`, SSL options are set. Local MySQL (in dev) uses plain connection.

## Config (env vars read in constructor)
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `APP_ENV` — triggers SSL config when `production`

## Gotchas
- Singleton means switching DB mid-request is impossible. Fine for this app (one DB), breaks if we ever need multi-tenant or read-replicas.
- `MYSQL_ATTR_SSL_CA` is empty string — this tells PDO to use system CAs rather than a bundled cert. Works on Render's PHP image. May need adjustment on Pi.
