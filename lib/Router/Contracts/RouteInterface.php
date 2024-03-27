<?php

namespace Lib\Router\Contracts;

use Lib\Router\Route;

interface RouteInterface
{
    public static function get($uri, $callback): Route;

    public static function post($uri, $callback): Route;

    public static function put($uri, $callback): Route;

    public static function patch($uri, $callback): Route;

    public static function delete($uri, $callback): Route;

    public static function options($uri, $callback): Route;

    public static function name(string $name): Route;

    public static function setPrefix($prefix): void;

    public static function middleware(array $middlewares = []): Route;

    public static function controller(string $controller): Route;

    public static function shouldExcludeCsrfForRoute(string $uri): bool;

    public static function loadMiddlewareConfig(): void;

    public static function getDefaultMiddlewares(): array;

    public static function group(callable $callback): void;

    public static function dispatch(): void;

    public static function getNamedRoutes(): array;
}
