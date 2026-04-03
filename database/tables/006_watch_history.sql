CREATE TABLE watch_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    content_id INT UNSIGNED NOT NULL,
    episode_id INT UNSIGNED NULL,
    status ENUM('in_progress', 'completed', 'dropped') NOT NULL DEFAULT 'in_progress',
    progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
    watch_count INT UNSIGNED NOT NULL DEFAULT 1,
    last_position_seconds INT UNSIGNED NULL,
    duration_seconds INT UNSIGNED NULL,
    last_watched_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Movies: one record per user+content (episode_id IS NULL)
    -- TV/Anime: one record per user+episode
    UNIQUE KEY uq_user_content_episode (user_id, content_id, episode_id),
    INDEX idx_user_last_watched (user_id, last_watched_at DESC),
    INDEX idx_user_status (user_id, status),
    CONSTRAINT fk_watch_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_watch_history_content FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    CONSTRAINT fk_watch_history_episode FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE SET NULL
) ENGINE=InnoDB;