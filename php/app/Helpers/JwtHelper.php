<?php

namespace App\Helpers;

use App\Config\Config;
use Firebase\JWT\JWT;

class JwtHelper
{
    public static function generateToken($user)
    {
        $payload = [
            'iss' => Config::getKey('APP_URL'),
            'aud' => Config::getKey('APP_URL'),
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600, // Token expires in 1 hour
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
            ],
        ];

        return JWT::encode($payload, Config::getKey('JWT_SECRET'), 'HS256');
    }

    public static function decodeToken($token)
    {
        $headers = new \stdClass();
        return JWT::decode($token, Config::getKey('JWT_SECRET'), $headers);
    }
}
