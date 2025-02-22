<?php

use App\Middleware\VerifyTurnstile;
use App\Middleware\EnsureAuthenticated;
use App\Middleware\EnsureCollectionOwner;
use App\Middleware\EnsureCollectionAccess;
use App\Middleware\EnsureCollectionStampAccess;

$router = App\Application\Router::getInstance();

$router->get('/api', [App\Controllers\ApiController::class, 'index']);
$router->get('/api/app', [App\Controllers\ApiController::class, 'app']);

$router->middleware(VerifyTurnstile::class, function () use ($router) {
    $router->post('/api/auth/register', [App\Controllers\AuthController::class, 'register']);
    $router->post('/api/auth/login', [App\Controllers\AuthController::class, 'login']);
    $router->post('/api/auth/reset-password', [App\Controllers\AuthController::class, 'resetPassword']);
});

$router->get('/api/collections', [App\Controllers\CollectionController::class, 'index']);

$router->middleware(EnsureCollectionStampAccess::class, function () use ($router) {
    $router->get('/api/stamps/{id}', [App\Controllers\StampController::class, 'get']);
});

$router->middleware(EnsureCollectionAccess::class, function () use ($router) {
    $router->get('/api/collections/{id}', [App\Controllers\CollectionController::class, 'get']);

    $router->post('/api/collections/{id}/stamps/create', [App\Controllers\StampController::class, 'create']);

    $router->middleware(EnsureCollectionOwner::class, function () use ($router) {
        $router->post('/api/collections/{id}/update', [App\Controllers\CollectionController::class, 'update']);
        $router->post('/api/collections/{id}/delete', [App\Controllers\CollectionController::class, 'delete']);

        $router->post('/api/collections/{id}/add-member', [App\Controllers\CollectionController::class, 'addMember']);
        $router->post('/api/collections/{id}/remove-member', [App\Controllers\CollectionController::class, 'removeMember']);
    });
});

$router->middleware(EnsureAuthenticated::class, function () use ($router) {
    $router->get('/api/me', [App\Controllers\MeController::class, 'index']);
    $router->post('/api/me/update', [App\Controllers\MeController::class, 'update']);
    $router->post('/api/me/update-password', [App\Controllers\MeController::class, 'updatePassword']);

    $router->post('/api/collections/create', [App\Controllers\CollectionController::class, 'create']);
});
