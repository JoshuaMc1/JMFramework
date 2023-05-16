<?php

namespace Lib;

use App\Middleware\Middleware;
use Lib\Http\ErrorHandler;

class Route
{
    private static $routes = [];
    private static $middlewares = [];

    private const GET_METHOD = 'GET';
    private const POST_METHOD = 'POST';
    private const PUT_METHOD = 'PUT';
    private const PATCH_METHOD = 'PATCH';
    private const DELETE_METHOD = 'DELETE';
    private const OPTIONS_METHOD = 'OPTIONS';

    public static function get($uri, $callback)
    {
        self::addRoute(self::GET_METHOD, $uri, $callback);
    }

    public static function post($uri, $callback)
    {
        self::addRoute(self::POST_METHOD, $uri, $callback);
    }

    public static function put($uri, $callback)
    {
        self::addRoute(self::PUT_METHOD, $uri, $callback);
    }

    public static function patch($uri, $callback)
    {
        self::addRoute(self::PATCH_METHOD, $uri, $callback);
    }

    public static function delete($uri, $callback)
    {
        self::addRoute(self::DELETE_METHOD, $uri, $callback);
    }

    public static function options($uri, $callback)
    {
        self::addRoute(self::OPTIONS_METHOD, $uri, $callback);
    }

    public static function middleware($middlewares)
    {
        try {
            $route = new static();
            $route::$middlewares = $middlewares;
            return $route;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function group(array $middlewares, callable $callback)
    {
        try {
            $route = new static();
            $route::$middlewares = $middlewares;
            $callback($route);
            $route::$middlewares = [];
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function dispatch()
    {
        try {
            $uri = trim($_SERVER['REQUEST_URI'], '/');
            $method = $_SERVER['REQUEST_METHOD'];
            $routes = self::$routes[$method] ?? [];
            $match = self::match($uri, $routes);

            if (!$match) {
                ErrorHandler::renderError(404, 'Page Not Found', 'Sorry, we couldn’t find the page you’re looking for.');
            }

            $middlewares = $match['middlewares'] ?? [];

            if (empty($middlewares)) {
                $middlewares = [Middleware::class];
            }

            $next = function () use ($match) {
                return self::execute($match);
            };

            $response = null;

            foreach ($middlewares as $middleware) {
                $middlewareInstance = new $middleware();
                $response = $middlewareInstance->handle($next);
            }

            if (is_array($response) || is_object($response)) {
                header('Content-Type: application/json');
                echo json_encode($response);
            } else {
                echo $response ?? '';
            }
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    private static function match($uri, $routes)
    {
        try {
            foreach ($routes as $route => $callback) {
                $pattern = self::prepareRoutePattern($route);

                if (preg_match($pattern, $uri, $matches)) {
                    return [
                        'callback' => $callback['callback'],
                        'params' => array_slice($matches, 1),
                        'middlewares' => $callback['middlewares'] ?? []
                    ];
                }
            }
            return false;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    private static function execute($match)
    {
        try {
            $callback = $match['callback'];
            $params = $match['params'];

            if (is_callable($callback)) {
                return $callback(...$params);
            } elseif (is_array($callback)) {
                $controller = new $callback[0];
                return $controller->{$callback[1]}(...$params);
            }
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    private static function addRoute($method, $uri, $callback)
    {
        self::$routes[$method][trim($uri, '/')] = [
            'callback' => $callback,
            'middlewares' => self::$middlewares
        ];
    }

    private static function prepareRoutePattern($route)
    {
        $pattern = preg_replace('#:[a-zA-Z]+#', '([^/]+)', $route);
        return "#^$pattern$#";
    }
}
