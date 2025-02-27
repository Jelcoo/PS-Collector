<?php

use App\Middleware\VerifyTurnstile;
use App\Middleware\EnsureAuthenticated;
use App\Middleware\EnsureCollectionOwner;
use App\Middleware\EnsureCollectionAccess;
use App\Middleware\EnsureCollectionStampAccess;

$router = App\Application\Router::getInstance();

$router->get('/api', [App\Controllers\ApiController::class, 'index']);

$router->middleware(VerifyTurnstile::class, function () use ($router) {
    $router->post('/api/auth/register', [App\Controllers\AuthController::class, 'register']);
    $router->post('/api/auth/login', [App\Controllers\AuthController::class, 'login']);
    $router->put('/api/auth/reset-password', [App\Controllers\AuthController::class, 'resetPassword']);
});

$router->get('/api/collections', [App\Controllers\CollectionController::class, 'index']);

$router->middleware(EnsureCollectionStampAccess::class, function () use ($router) {
    $router->get('/api/stamps/{id}', [App\Controllers\StampController::class, 'get']);
    $router->put('/api/stamps/{id}', [App\Controllers\StampController::class,'update']);
    $router->delete('/api/stamps/{id}', [App\Controllers\StampController::class,'delete']);
});

$router->middleware(EnsureCollectionAccess::class, function () use ($router) {
    $router->get('/api/collections/{id}', [App\Controllers\CollectionController::class, 'get']);

    $router->middleware(EnsureCollectionOwner::class, function () use ($router) {
        $router->put('/api/collections/{id}', [App\Controllers\CollectionController::class, 'update']);
        $router->delete('/api/collections/{id}', [App\Controllers\CollectionController::class, 'delete']);

        $router->post('/api/collections/{id}/stamps', [App\Controllers\StampController::class, 'create']);

        $router->post('/api/collections/{id}/members', [App\Controllers\CollectionController::class, 'addMember']);
        $router->delete('/api/collections/{id}/members/{memberId}', [App\Controllers\CollectionController::class, 'removeMember']);
    });
});

$router->middleware(EnsureAuthenticated::class, function () use ($router) {
    $router->get('/api/me', [App\Controllers\MeController::class, 'index']);
    $router->put('/api/me', [App\Controllers\MeController::class, 'update']);
    $router->put('/api/me/password', [App\Controllers\MeController::class, 'updatePassword']);

    $router->post('/api/collections', [App\Controllers\CollectionController::class, 'create']);
});
