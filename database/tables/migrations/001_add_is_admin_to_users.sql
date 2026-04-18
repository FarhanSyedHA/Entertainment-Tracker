-- Add is_admin flag to users. Defaults to 0 so existing + new users are
-- non-admin by default; the project owner gets promoted via a manual UPDATE.
--
-- To promote yourself (run against TiDB Cloud):
--   UPDATE users SET is_admin = 1 WHERE email = 'your-email@example.com';
ALTER TABLE users
  ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0
  AFTER password;
