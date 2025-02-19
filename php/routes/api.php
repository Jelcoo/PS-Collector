<?php

use App\Middleware\EnsureAuthenticated;
use App\Middleware\EnsureCollectionAccess;
use App\Middleware\VerifyTurnstile;

$router = App\Application\Router::getInstance();

$router->get('/api', [App\Controllers\ApiController::class, 'index']);
$router->get('/api/app', [App\Controllers\ApiController::class, 'app']);

$router->middleware(VerifyTurnstile::class, function () use ($router) {
    $router->post('/api/auth/register', [App\Controllers\AuthController::class, 'register']);
    $router->post('/api/auth/login', [App\Controllers\AuthController::class, 'login']);
    $router->post('/api/auth/reset-password', [App\Controllers\AuthController::class, 'resetPassword']);
});

$router->get('/api/collections', [App\Controllers\CollectionController::class, 'index']);

$router->middleware(EnsureCollectionAccess::class, function () use ($router) {
    $router->get('/api/collections/{id}', [App\Controllers\CollectionController::class, 'get']);
});

$router->middleware(EnsureAuthenticated::class, function () use ($router) {
    $router->get('/api/me', [App\Controllers\ApiController::class, 'me']);

    $router->post('/api/collections/create', [App\Controllers\CollectionController::class, 'create']);
});
