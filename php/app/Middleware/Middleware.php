<?php

namespace App\Middleware;

use App\Application\Response;

class Middleware
{
    protected function unauthorized(): void
    {
        $response = new Response();
        $response->setStatusCode(401);
        $response->setContent([
            'success' => false,
            'error' => 'Unauthorized',
        ]);
        $response->sendJson();
        exit;
    }

    protected function forbidden(): void
    {
        $response = new Response();
        $response->setStatusCode(403);
        $response->setContent([
            'success' => false,
            'error' => 'Forbidden',
        ]);
        $response->sendJson();
        exit;
    }

    protected function notFound(): void
    {
        $response = new Response();
        $response->setStatusCode(404);
        $response->setContent([
            'success' => false,
            'error' => 'Not Found',
        ]);
        $response->sendJson();
        exit;
    }
}
