<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\Config;

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

    public static function isValidToken($token): bool
    {
        try {
            JWT::decode($token, new Key(Config::getKey('JWT_SECRET'), 'HS256'));
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    public static function getSessionUser(): int
    {
        $jwtToken = explode(' ', $_SERVER['HTTP_AUTHORIZATION'])[1];
        $decoded = JWT::decode($jwtToken, new Key(Config::getKey('JWT_SECRET'), 'HS256'));

        return $decoded->data->id ?? null;
    }
}
