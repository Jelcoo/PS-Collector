<?php

namespace App\Controllers;

class ErrorController extends Controller
{
    public function error404(): array
    {
        return [
            'status' => 404,
            'message' => 'Page not found',
        ];
    }

    public function error500(string $error): array
    {
        return [
            'status' => 500,
            'message' => $error,
        ];
    }
}
