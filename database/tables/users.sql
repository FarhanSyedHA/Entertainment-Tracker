-- Users table: stores account info for authentication.
-- Password is bcrypt hashed in PHP before storing, the DB just holds the hash string.
-- Email is the login identifier and must be unique.
-- Timestamps track when the account was created and last modified.
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL COMMENT 'Display name, does not have to be unique',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Used for login, must be unique',
    password VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed, never stored as plain text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
