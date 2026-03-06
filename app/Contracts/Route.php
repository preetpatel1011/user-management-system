<?php

namespace App\Contracts;

use App\Core\Router;

class Route
{
    private static ?Router $router = null;

    private static function getRouter(): Router
    {
        if (self::$router === null) {
            self::$router = new Router();
        }
        return self::$router;
    }

    public static function __callStatic(string $method, array $args): mixed
    {
        return self::getRouter()->$method(...$args);
    }
}