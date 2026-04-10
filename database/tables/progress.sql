-- Progress table: tracks what a user has watched and how far.
-- For movies: episode_id is NULL, tracks at content level.
-- For TV/anime: episode_id points to the specific episode being tracked.
-- No season_id needed — we JOIN through episode.season_id to find the season.
-- Status is per-record. Season/show completion is calculated from episode data, not stored.
-- last_position_seconds stores how far they watched; percentage is derived (position / duration * 100).
-- favourite is content-level only — you favourite a show, not an episode.
-- Stremio auto-tracking updates last_position_seconds; 80% = auto-marked as completed.
-- Manual add sets status directly to 'completed'.
CREATE TABLE IF NOT EXISTS progress (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL COMMENT 'Who is watching',
    content_id INT UNSIGNED NOT NULL COMMENT 'What they are watching',
    episode_id INT UNSIGNED DEFAULT NULL COMMENT 'Which episode, null for movies',
    status ENUM('in_progress', 'completed', 'dropped') NOT NULL DEFAULT 'in_progress',
    favourite BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'User can favourite at content level only',
    last_position_seconds INT UNSIGNED DEFAULT 0 COMMENT 'How far they watched, used to calculate percentage',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episode(id) ON DELETE CASCADE,
    UNIQUE KEY unique_movie_progress (user_id, content_id, episode_id) COMMENT 'One record per user per movie or per episode'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
