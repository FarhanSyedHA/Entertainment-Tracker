<?php
// This file is where every frontend HTTP request first hits, because the PHP server we configured in Dockerfile points here ( -t public ).

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/app.php';

use App\Core\Router;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Actions\Test;
use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Content\TrendingShowsAction;
use App\Actions\Content\ContentDetailsAction;
use App\Actions\Search\SearchAction;
use App\Actions\WatchHistory\MarkAsWatchedAction;
use App\Actions\WatchHistory\UnmarkWatchedAction;
use App\Actions\WatchHistory\WatchStatusAction;
use App\Actions\WatchHistory\GetWatchedAction;
use App\Actions\WatchHistory\GetInProgressAction;

$router = new Router();

$router
->addRoute( 'POST', '/api/register', RegisterAction::class, [ CorsMiddleware::class ] )
->addRoute( 'POST', '/api/login', LoginAction::class, [ CorsMiddleware::class ] )
->addRoute( 'GET', '/api/get-trending-shows', TrendingShowsAction::class, [ CorsMiddleware::class ] )
->addRoute( 'GET', '/api/content-details', ContentDetailsAction::class, [ CorsMiddleware::class ] )
->addRoute( 'GET', '/api/search', SearchAction::class, [CorsMiddleware::class])
->addRoute( 'POST', '/api/watch', MarkAsWatchedAction::class, [CorsMiddleware::class, AuthMiddleware::class])
->addRoute( 'POST', '/api/unwatch', UnmarkWatchedAction::class, [CorsMiddleware::class, AuthMiddleware::class])
->addRoute( 'POST', '/api/watch-status', WatchStatusAction::class, [CorsMiddleware::class, AuthMiddleware::class])
->addRoute( 'GET', '/api/watched-shows', GetWatchedAction::class, [CorsMiddleware::class, AuthMiddleware::class])
->addRoute( 'GET', '/api/in-progress-shows', GetInProgressAction::class, [CorsMiddleware::class, AuthMiddleware::class])
->addRoute( 'GET', '/api/content', Test::class, [ CorsMiddleware::class, AuthMiddleware::class ] );

$request = new Request();
$router->resolve( $request );
