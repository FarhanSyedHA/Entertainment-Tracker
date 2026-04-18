-- Admin tasks: a lightweight in-app todo board for the project owner.
-- Only users with users.is_admin = 1 can read or write rows. The table has
-- no user_id FK because all tasks are shared across admins (there's only
-- one admin in practice, and making it per-user would split the board if
-- we ever add a second).
CREATE TABLE IF NOT EXISTS admin_tasks (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    type ENUM('feature','bug','maintenance') NOT NULL DEFAULT 'feature',
    status ENUM('open','done') NOT NULL DEFAULT 'open',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_created_by (created_by),
    CONSTRAINT fk_admin_tasks_created_by
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
