<?php

namespace Lib;

class Route
{
    private static $routes = [];

    public static function get($uri, $callback)
    {
        self::$routes['GET'][trim($uri, '/')] = $callback;
    }

    public static function post($uri, $callback)
    {
        self::$routes['POST'][trim($uri, '/')] = $callback;
    }

    public static function dispatch()
    {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $method = $_SERVER['REQUEST_METHOD'];
        $routes = self::$routes[$method];
        $match = self::match($uri, $routes);

        if ($match) {
            $response = self::execute($match);

            if (is_array($response) || is_object($response)) {
                header('Content-Type: application/json');
                echo json_encode($response);
            } else {
                echo $response;
            }
        } else {
            echo '404 Not Found';
        }
    }

    private static function match($uri, $routes)
    {
        foreach ($routes as $route => $callback) {
            if (strpos($route, ':') !== false) {
                $route = preg_replace('#:[a-zA-Z]+#', '([a-zA-Z]+)', $route);
            }
            if (preg_match("#^$route$#", $uri, $matches)) {
                return [
                    'callback' => $callback,
                    'params' => array_slice($matches, 1)
                ];
            }
        }
        return false;
    }

    private static function execute($match)
    {
        $callback = $match['callback'];
        $params = $match['params'];

        if (is_callable($callback)) {
            return $callback(...$params);
        } elseif (is_array($callback)) {
            $controller = new $callback[0];
            return $controller->{$callback[1]}(...$params);
        }
    }
}
