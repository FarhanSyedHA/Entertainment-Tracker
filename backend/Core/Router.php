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
