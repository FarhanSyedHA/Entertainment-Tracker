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

    //frontend calls fetch and the browser sees different origin and sends in OPTIONS method which is to retrieve the cors details that says whos allowed and what methods. 
    if($method === 'OPTIONS') {
      $origin = getenv('CORS_ORIGIN') ?: '*';
      header('Access-Control-Allow-Origin: ' . $origin);
      header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
      header('Access-Control-Allow-Headers: Content-Type, Authorization');
      header('Access-Control-Allow-Credentials: true');
      http_response_code(200);
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
