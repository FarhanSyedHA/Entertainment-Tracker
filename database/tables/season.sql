-- Season table: links to content for TV shows and anime only.
-- Movies don't have seasons, so they won't have rows here.
-- season_number is the real-world number (Season 1, Season 2), not the DB auto-increment id.
-- episode_count is NOT stored — we calculate it with COUNT(*) from the episode table.
-- Season completion status is NOT stored — derived from whether all its episodes are completed.
-- UNIQUE constraint on (content_id, season_number) prevents duplicate seasons for the same show.
CREATE TABLE IF NOT EXISTS season (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    content_id INT UNSIGNED NOT NULL COMMENT 'Which show this season belongs to',
    season_number SMALLINT UNSIGNED NOT NULL COMMENT 'Real-world season number, not the DB id',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    UNIQUE KEY unique_season (content_id, season_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
