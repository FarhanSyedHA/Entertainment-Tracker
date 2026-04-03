CREATE TABLE episodes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    season_id INT UNSIGNED NOT NULL,
    episode_number INT UNSIGNED NOT NULL,
    name VARCHAR(255) NULL,
    overview TEXT NULL,
    still_url VARCHAR(1000) NULL,
    air_date DATE NULL,
    runtime_minutes INT UNSIGNED NULL,
    UNIQUE KEY uq_season_episode (season_id, episode_number),
    CONSTRAINT fk_episodes_season FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE
) ENGINE=InnoDB;