<?php
namespace App\Middleware;

use App\Core\Request;

class CorsMiddleware implements MiddlewareInterface
{
  public function handle(Request $request): void
  {
    $origin = getenv('CORS_ORIGIN') ?: '*';

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');

    // Preflight: browser sends OPTIONS first to check permissions.
    // Respond 200 immediately without running the action.
    if ($request->getMethod() === 'OPTIONS') {
      http_response_code(200);
      exit;
    }
  }
}
