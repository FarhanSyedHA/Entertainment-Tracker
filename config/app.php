<?php
// Load .env.development ONLY for local dev. In production (Render/etc.) the real
// env vars are injected by the platform and must win — so we skip the file-load
// step entirely. Without this skip, putenv() here would stomp CORS_ORIGIN /
// DB_HOST / TMDB_API_KEY etc. with dev values that got baked into the image.
$path = __DIR__ . '/../.env.development';
$isProd = getenv('APP_ENV') === 'production' || getenv('RENDER') !== false;

if (!$isProd) {
  if (!file_exists($path)) {
    throw new \RuntimeException('Missing .env.development file');
  }

  $allLinesArray = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($allLinesArray as $line) {
    $line = trim($line);
    if (str_starts_with($line, '#')) continue;
    if ($line === '') continue;
    putenv(rtrim($line, "\r\n"));
  }
}