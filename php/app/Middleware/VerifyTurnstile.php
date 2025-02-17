<?php

namespace App\Middleware;

use App\Helpers\JwtHelper;
use App\Services\TurnstileService;

class VerifyTurnstile extends Middleware implements MiddlewareInterface
{
    private TurnstileService $turnstileService;

    public function __construct()
    {
        $this->turnstileService = new TurnstileService();
    }

    public function verify(array $params = []): bool
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $data['cf-turnstile-response'] ?? '';

        if ($this->turnstileService->verify($token) === false) {
            $this->forbidden('Turnstile verification failed');
        }

        return true;
    }
}
