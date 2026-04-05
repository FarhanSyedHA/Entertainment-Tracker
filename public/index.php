<?php
// this file is where every frontend http request first hits. because the php server we configured in Dockerfile points here (-t public)

require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Router;
use App\Core\Request;
use App\Actions\Test;

$router = new Router();
$router->addRoute('GET', '/api/content', Test::class);

$request = new Request();
$router->resolve($request);