<?php

namespace Lib;

class Route
{
    /* Create array of empty routes. */
    private static $routes = [];
    /* Constant stored by the GET method */
    private const GET_METHOD = 'GET';
    /* Constant stored by the POST method */
    private const POST_METHOD = 'POST';

    /**
     * It takes a URI and a callback function and adds it to the routes array
     * 
     * @param uri The URI that the user will type in the browser.
     * @param callback The callback function to be executed when the route is matched.
     */
    public static function get($uri, $callback)
    {
        self::$routes[self::GET_METHOD][trim($uri, '/')] = $callback;
    }

    /**
     * It takes a URI and a callback function as parameters and adds them to the routes array
     * 
     * @param uri The URI to match
     * @param callback The callback function to be executed when the route is matched.
     */
    public static function post($uri, $callback)
    {
        self::$routes[self::POST_METHOD][trim($uri, '/')] = $callback;
    }

    /**
     * It takes the request URI, the request method, and the routes array, and if it finds a match, it
     * executes the callback function and returns the response.
     * 
     * The first thing we do is trim the request URI of any leading or trailing slashes. We then get
     * the request method and the routes array.
     * 
     * Next, we call the match() function, which we'll look at in a moment. If it returns a match, we
     * call the execute() function, which we'll also look at in a moment.
     * 
     * If the response is an array or an object, we set the Content-Type header to application/json and
     * echo the response as JSON. Otherwise, we just echo the response.
     * 
     * If there is no match, we set the HTTP status code to 404 and echo a message.
     */
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
            header('HTTP/1.0 404 Not Found');
            echo '404 Not Found';
        }
    }

    /**
     * It takes a URI and an array of routes, and returns an array containing the callback and the
     * parameters if a match is found, or false if no match is found
     * 
     * @param uri The URI that was requested.
     * @param routes This is the array of routes that we want to match against.
     */
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

    /**
     * It takes a callback and a list of parameters, and if the callback is callable, it calls it with
     * the parameters, otherwise it instantiates a controller and calls the method on it with the
     * parameters.
     * 
     * @param match The array of matches from the route.
     * 
     * @return The callback function is being returned.
     */
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
