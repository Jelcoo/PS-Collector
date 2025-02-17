<?php

namespace App\Middleware;

use App\Application\Response;

class Middleware
{
    protected function unauthorized($msg = 'Unauthorized'): void
    {
        $response = new Response();
        $response->setStatusCode(401);
        $response->setContent([
            'success' => false,
            'error' => $msg,
        ]);
        $response->sendJson();
        exit;
    }

    protected function forbidden($msg = 'Forbidden'): void
    {
        $response = new Response();
        $response->setStatusCode(403);
        $response->setContent([
            'success' => false,
            'error' => $msg,
        ]);
        $response->sendJson();
        exit;
    }

    protected function notFound($msg = 'Not Found'): void
    {
        $response = new Response();
        $response->setStatusCode(404);
        $response->setContent([
            'success' => false,
            'error' => $msg,
        ]);
        $response->sendJson();
        exit;
    }
}
