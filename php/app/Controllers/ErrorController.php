<?php

namespace App\Controllers;

class ErrorController extends Controller
{
    public function error404(): array
    {
        return [
            'code' => 404,
            'message' => 'Page not found'
        ];
    }

    public function error500(string $error): array
    {
        return [
            'code' => 500,
            'message' => $error
        ];
    }
}
