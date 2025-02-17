<?php

namespace App\Middleware;

use App\Helpers\JwtHelper;

class EnsureAuthenticated extends Middleware implements MiddlewareInterface
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
}
