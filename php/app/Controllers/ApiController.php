<?php

namespace App\Controllers;

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
}
