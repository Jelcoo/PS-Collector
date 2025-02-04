<?php

namespace App\Middleware;

use App\Application\Response;
use App\Helpers\JwtHelper;

class EnsureAuthenticated implements Middleware
{
    public function verify(array $params = []): bool
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->unauthorized();
        }

        $authHeader = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
        if ($authHeader[0] !== 'Bearer' || empty($authHeader[1])) {
            $this->unauthorized();
        }
        $jwt = $authHeader[1];

        if (!JwtHelper::isValidToken($jwt)) {
            $this->unauthorized();
        }

        return true;
    }

    private function unauthorized(): void
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
}
