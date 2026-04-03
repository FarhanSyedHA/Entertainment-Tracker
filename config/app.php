<?php

return [
    'db_host' => getenv('DB_HOST') ?: '127.0.0.1',
    'db_port' => getenv('DB_PORT') ?: '3306',
    'db_name' => getenv('DB_NAME') ?: 'entertainment_tracker',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') ?: '',
    'app_env' => getenv('APP_ENV') ?: 'development',
    'app_secret' => getenv('APP_SECRET') ?: 'change-me',
    'cors_origin' => getenv('CORS_ORIGIN') ?: 'http://localhost:3000',
];
