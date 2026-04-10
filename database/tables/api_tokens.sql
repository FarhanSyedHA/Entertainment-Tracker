-- API tokens table: stores Bearer tokens for authentication.
-- When a user logs in, PHP generates a random token and stores it here.
-- The frontend sends this token with every request (Authorization: Bearer <token>).
-- The backend looks it up to identify who is making the request.
-- Same mechanism used by the Stremio addon to authenticate when sending watch progress.
-- created_at is useful for expiring old tokens in the future.
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL COMMENT 'Which user this token belongs to',
    token VARCHAR(255) NOT NULL UNIQUE COMMENT 'Random string used as Bearer token for auth',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When the token was issued, useful for expiry',
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
