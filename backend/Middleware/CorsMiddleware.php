<?php
namespace App\Middleware;

use App\Core\Request;

class CorsMiddleware implements MiddlewareInterface
{
  public function handle(Request $request): void
  {
    $origin = getenv('CORS_ORIGIN') ?: '*'; //accept from the frontend only (this is the port i run)

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
  }
}
