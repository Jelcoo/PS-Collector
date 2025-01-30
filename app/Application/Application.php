<?php

namespace App\Application;

class Application
{
    private static Application $application;

    public static function getInstance(): Application
    {
        if (!isset(self::$application)) {
            self::$application = new Application();
        }

        return self::$application;
    }

    public function run(): void
    {
        $router = Router::getInstance();
        $router->resolve();
    }
}
