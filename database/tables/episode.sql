-- Episode table: individual episodes linked to a season.
-- This is the lowest level of tracking for TV/anime — progress is tracked per episode.
-- duration_seconds lives here (not on content) because each episode can have different lengths.
-- episode_number is the real-world number, separate from the DB id.
-- The season can be found via season_id, and the show via season.content_id — no need to store content_id here.
CREATE TABLE IF NOT EXISTS episode (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    season_id INT UNSIGNED NOT NULL COMMENT 'Which season this episode belongs to',
    episode_number SMALLINT UNSIGNED NOT NULL COMMENT 'Real-world episode number, not the DB id',
    title VARCHAR(500) DEFAULT NULL COMMENT 'Episode title, if available from API',
    duration_seconds INT UNSIGNED DEFAULT NULL COMMENT 'Episode runtime in seconds',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (season_id) REFERENCES season(id) ON DELETE CASCADE,
    UNIQUE KEY unique_episode (season_id, episode_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
