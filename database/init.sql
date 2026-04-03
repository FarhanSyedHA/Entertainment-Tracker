-- Entertainment Tracker — Database Init
-- MySQL 8.0+

CREATE DATABASE IF NOT EXISTS entertainment_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE entertainment_tracker;

-- Load tables in dependency order
SOURCE tables/001_users.sql;
SOURCE tables/002_user_settings.sql;
SOURCE tables/003_content.sql;
SOURCE tables/004_seasons.sql;
SOURCE tables/005_episodes.sql;
SOURCE tables/006_watch_history.sql;
SOURCE tables/007_api_tokens.sql;
