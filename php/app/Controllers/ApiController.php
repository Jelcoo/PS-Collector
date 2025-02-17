<?php

namespace App\Controllers;

use App\Config\Config;

class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): array
    {
        return [
            'message' => 'Hello World!',
        ];
    }

    public function me(): array
    {
        return [
            'user' => $this->getSession()->toArray(),
        ];
    }

    public function app(): array
    {
        return [
            'turnstile_key' => Config::getKey('TURNSTILE_KEY'),
        ];
    }
}
