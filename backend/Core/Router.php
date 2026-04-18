<?php
namespace App\Core;

class Router
{
  private array $routes;

  public function __construct()
  {
    $this->routes = [];
  }

  public function addRoute(string $method, string $path, string $handler, array $middleware = []): self
  {
    $this->routes[$method][$path] = [
      'handler' => $handler,
      'middleware' => $middleware,
    ];
    return $this;
  }

  public function resolve(Request $request): void
  {
    $method = $request->getMethod();
    $path = $request->getPath();

    // CORS preflight. Browsers send OPTIONS before the real request to learn which
    // origins/methods/headers the server accepts. We look up the *real* route (by the
    // requested method from Access-Control-Request-Method) and run its CORS middleware
    // so the response headers match what the actual request will get. CorsMiddleware
    // handles the exit for OPTIONS itself.
    if ($method === 'OPTIONS') {
      $requestedMethod = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? 'GET';
      $route = $this->routes[$requestedMethod][$path] ?? null;
      $middlewares = $route['middleware'] ?? [\App\Middleware\CorsMiddleware::class];
      foreach ($middlewares as $middlewareClass) {
        if ($middlewareClass === \App\Middleware\CorsMiddleware::class) {
          (new $middlewareClass())->handle($request);
        }
      }
      // If CorsMiddleware wasn't in the list (shouldn't happen), fall through with a 204.
      http_response_code(204);
      exit;
    }

    $route = $this->routes[$method][$path] ?? null;

    if (!$route) {
      Response::notFound();
    }

    foreach ($route['middleware'] as $middlewareClass) {
      $middleware = new $middlewareClass();
      $middleware->handle($request);
    }

    $action = new $route['handler']();
    $action->handle($request);
  }
}
