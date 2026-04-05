<?php
namespace App\Core;

class Router
{
  private array $routes;

  public function __construct() {
    $this->routes = [];
  }

  public function addRoute(string $method,string $path, string $handler): void
  {
    $this->routes[$method][$path] = $handler;
  } 

  public function resolve(Request $request) {
    $method = $request->getMethod();
    $path = $request->getPath();
    $handler = $this->routes[$method][$path] ?? null;

    if(!$handler) {
      $response = new Response;
      $response->json(['status' => 'failed'], 404);
      return;
    }

    $action = new $handler();
    $action->handle($request);
  }
}