<?php

$router = App\Application\Router::getInstance();

$router->get('/api', [App\Controllers\ApiController::class, 'index']);

$router->post('/api/auth/register', [App\Controllers\AuthController::class, 'register']);
$router->post('/api/auth/login', [App\Controllers\AuthController::class, 'login']);
