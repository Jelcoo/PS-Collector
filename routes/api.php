<?php

$router = App\Application\Router::getInstance();

$router->get('/api', [App\Controllers\ApiController::class, 'index']);
