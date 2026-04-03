CREATE TABLE seasons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_id INT UNSIGNED NOT NULL,
    season_number INT UNSIGNED NOT NULL,
    name VARCHAR(255) NULL,
    overview TEXT NULL,
    poster_url VARCHAR(1000) NULL,
    air_date DATE NULL,
    episode_count INT UNSIGNED NULL,
    UNIQUE KEY uq_content_season (content_id, season_number),
    CONSTRAINT fk_seasons_content FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE
) ENGINE=InnoDB;