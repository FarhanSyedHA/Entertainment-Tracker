-- Content table: cached metadata from TMDB/Jikan/OMDB APIs.
-- One table for all types (movie, tv, anime) — filtered by the type enum column.
-- No user_id here — content is shared data that exists regardless of who watches it.
-- The progress table links users to content.
-- duration_seconds is only for movies; TV/anime duration lives on the episode table.
-- tmdb_id and jikan_id are nullable because content comes from one API or the other, not both.
-- Trending/popular content is fetched from APIs at runtime, not stored here.
CREATE TABLE IF NOT EXISTS content (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    type ENUM('movie', 'tv', 'anime') NOT NULL COMMENT 'What kind of content this is',
    title VARCHAR(500) NOT NULL,
    description TEXT DEFAULT NULL COMMENT 'Synopsis of the content',
    release_year SMALLINT UNSIGNED DEFAULT NULL,
    imdb_rating DECIMAL(3,1) DEFAULT NULL COMMENT 'Rating out of 10, e.g. 8.5',
    thumbnail_url VARCHAR(1000) DEFAULT NULL COMMENT 'Poster image URL from TMDB/Jikan',
    duration_seconds INT UNSIGNED DEFAULT NULL COMMENT 'Total runtime in seconds, only used for movies',
    tmdb_id INT UNSIGNED DEFAULT NULL COMMENT 'The Movie Database ID, null if content is from Jikan',
    jikan_id INT UNSIGNED DEFAULT NULL COMMENT 'MyAnimeList ID via Jikan, null if content is from TMDB',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_type (type),
    INDEX idx_tmdb_id (tmdb_id),
    INDEX idx_jikan_id (jikan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
