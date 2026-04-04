<?php

use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\MeAction;
use App\Actions\Content\SearchAction;
use App\Actions\Content\FetchAction;
use App\Actions\Content\GetAction;
use App\Actions\WatchHistory\ListAction;
use App\Actions\WatchHistory\AddAction;
use App\Actions\WatchHistory\UpdateAction;
use App\Actions\WatchHistory\DeleteAction;
use App\Core\Middleware\AuthMiddleware;

return [
    // Auth
    'POST /api/auth/register' => [
        'action' => RegisterAction::class,
        'middleware' => [],
    ],
    'POST /api/auth/login' => [
        'action' => LoginAction::class,
        'middleware' => [],
    ],
    'POST /api/auth/logout' => [
        'action' => LogoutAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
    'GET /api/auth/me' => [
        'action' => MeAction::class,
        'middleware' => [AuthMiddleware::class],
    ],

    // Content
    'GET /api/content/search' => [
        'action' => SearchAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
    'POST /api/content/fetch' => [
        'action' => FetchAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
    'GET /api/content/{id}' => [
        'action' => GetAction::class,
        'middleware' => [AuthMiddleware::class],
    ],

    // Watch History
    'GET /api/watch-history' => [
        'action' => ListAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
    'POST /api/watch-history' => [
        'action' => AddAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
    'PUT /api/watch-history/{id}' => [
        'action' => UpdateAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
    'DELETE /api/watch-history/{id}' => [
        'action' => DeleteAction::class,
        'middleware' => [AuthMiddleware::class],
    ],
];
