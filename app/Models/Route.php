<?php

namespace App\Models;

class Route
{
    public string $uri;
    public string $method;
    public array $callback;
    public array $middleware;
    public array $params = [];

    public function __construct(string $uri, string $method, array $callback, array $middleware)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->callback = $callback;
        $this->middleware = $middleware;
    }

    public function executeMiddleware(array $params = []): bool
    {
        foreach ($this->middleware as $middleware) {
            if (!$middleware->verify($params)) {
                return false;
            }
        }

        return true;
    }
}
