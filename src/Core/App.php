<?php

namespace App\Core;

use App\Core\Exceptions\HttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Middleware\CorsMiddleware;

class App
{
    public static function run(): void
    {
        $request = new Request();

        // Handle CORS
        $cors = new CorsMiddleware();
        $corsResponse = $cors->handle($request, function (Request $req) {
            return self::handleRequest($req);
        });

        $corsResponse->send();
    }

    private static function handleRequest(Request $request): Response
    {
        try {
            $routes = require __DIR__ . '/../../config/routes.php';
            $router = new Router($routes);
            return $router->dispatch($request);
        } catch (ValidationException $e) {
            return Response::json([
                'error' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatusCode());
        } catch (HttpException $e) {
            return Response::error($e->getMessage(), $e->getStatusCode());
        }
    }
}
