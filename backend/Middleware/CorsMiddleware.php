<?php
namespace App\Middleware;

use App\Core\Request;

class CorsMiddleware implements MiddlewareInterface
{
  public function handle(Request $request): void
  {
    // CORS_ORIGIN is a comma-separated allowlist. Vercel preview deploys get unique
    // URLs per build, so we match by suffix (e.g. ".vercel.app") in addition to exact match.
    $configured = getenv('CORS_ORIGIN') ?: '*';
    $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = $this->resolveOrigin($configured, $requestOrigin);

    header('Access-Control-Allow-Origin: ' . $allowed);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
      http_response_code(204);
      exit;
    }
  }

  private function resolveOrigin(string $configured, string $requestOrigin): string
  {
    if ($configured === '*' || $requestOrigin === '') {
      return $configured;
    }
    foreach (array_map('trim', explode(',', $configured)) as $entry) {
      if ($entry === '') continue;
      if (str_starts_with($entry, '*.')) {
        $suffix = substr($entry, 1); // "*.vercel.app" -> ".vercel.app"
        if (str_ends_with($requestOrigin, $suffix)) return $requestOrigin;
      } elseif ($entry === $requestOrigin) {
        return $requestOrigin;
      }
    }
    return $configured; // fall back to first/default — will cause a CORS error, which is correct
  }
}
