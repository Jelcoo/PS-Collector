<?php

namespace App\Middleware;

interface MiddlewareInterface
{
    public function verify(array $params = []): bool;
}
