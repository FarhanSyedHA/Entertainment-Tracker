<?php
$path = __DIR__ . '/../.env.development';
if(!file_exists($path)) {
  throw new \RuntimeException('Missing .env.development file');
}

$allLinesArray = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($allLinesArray as $line) {
  $line = trim($line);
  if (str_starts_with($line, '#')) continue;
  if ($line === '') continue;
  putenv($line);
}