<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $pattern => $config) {
            [$routeMethod, $routePath] = explode(' ', $pattern, 2);

            if ($method !== $routeMethod) {
                continue;
            }

            $regex = $this->pathToRegex($routePath);

            if (preg_match($regex, $uri, $matches)) {
                // Set named route params on request
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $request->setParam($key, $value);
                    }
                }

                return $this->runMiddlewareChain(
                    $request,
                    $config['middleware'] ?? [],
                    $config['action']
                );
            }
        }

        throw new NotFoundException('Route not found');
    }

    private function pathToRegex(string $path): string
    {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    private function runMiddlewareChain(Request $request, array $middlewareClasses, string $actionClass): Response
    {
        $action = new $actionClass();

        $next = fn(Request $req) => $action($req);

        foreach (array_reverse($middlewareClasses) as $middlewareClass) {
            $middleware = new $middlewareClass();
            $currentNext = $next;
            $next = fn(Request $req) => $middleware->handle($req, $currentNext);
        }

        return $next($request);
    }
}
