<?php

namespace Lib;

use App\Http\Middleware\Middleware;
use Lib\Exception\ExceptionHandler;
use Lib\Exception\RouteExceptions\{
    MiddlewareException,
    PageNotFoundException,
    InternalServerErrorException,
    InvalidRouteConfigurationException,
    MethodNotAllowedException
};
use Lib\Http\Request;

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

    public function __construct()
    {
        error_reporting(E_ERROR);
        ini_set('display_errors', 0);
        $this->checkAppKey();
    }

    public static function get($uri, $callback)
    {
        self::addRoute(self::GET_METHOD, $uri, $callback);
        return new static();
    }

    public static function post($uri, $callback)
    {
        self::addRoute(self::POST_METHOD, $uri, $callback);
        return new static();
    }

    public static function put($uri, $callback)
    {
        self::addRoute(self::PUT_METHOD, $uri, $callback);
        return new static();
    }

    public static function patch($uri, $callback)
    {
        self::addRoute(self::PATCH_METHOD, $uri, $callback);
        return new static();
    }

    public static function delete($uri, $callback)
    {
        self::addRoute(self::DELETE_METHOD, $uri, $callback);
        return new static();
    }

    public static function options($uri, $callback)
    {
        self::addRoute(self::OPTIONS_METHOD, $uri, $callback);
        return new static();
    }

    public static function middleware($middlewares)
    {
        try {
            $route = new static();
            $route::$middlewares = $middlewares;
            return $route;
        } catch (\Throwable  $th) {
            ExceptionHandler::handleException(new MiddlewareException($th->getMessage()));
        }
    }

    public function addMiddlewareToRoute($middlewares)
    {
        $this::$middlewares = $middlewares;
        return $this;
    }

    public static function group(array $middlewares, callable $callback)
    {
        try {
            $route = new static();
            $route::$middlewares = $middlewares;
            $callback($route);
            $route::$middlewares = [];
        } catch (\Throwable $th) {
            ExceptionHandler::handleException(new MiddlewareException($th->getMessage()));
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
                throw new PageNotFoundException();
            }

            $middlewares = $match['middlewares'] ?? [];

            if (empty($middlewares)) {
                $middlewares = [Middleware::class];
            }

            $next = function () use ($match) {
                return self::execute($match);
            };

            $response = null;
            $request = new Request();

            foreach ($middlewares as $middleware) {
                $middlewareInstance = new $middleware();
                $response = $middlewareInstance->handle($next, $request);
            }

            if (is_array($response) || is_object($response)) {
                echo response()->json($response)->send();
            } else {
                echo $response ?? '';
            }
        } catch (PageNotFoundException | InternalServerErrorException | MethodNotAllowedException | \Throwable $e) {
            ExceptionHandler::handleException($e);
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

            throw new PageNotFoundException();
        } catch (PageNotFoundException $e) {
            ExceptionHandler::handleException($e);
        }
    }

    private static function execute($match)
    {
        try {
            $callback = $match['callback'];
            $params = $match['params'];

            if (is_callable($callback)) {
                $reflector = new \ReflectionFunction($callback);
                $parameters = $reflector->getParameters();
                $args = [];

                foreach ($parameters as $parameter) {
                    $className = $parameter->getType()->getName();

                    if ($className === Request::class) {
                        $args[] = new Request();
                    } else {
                        $args[] = null;
                    }
                }

                return call_user_func_array($callback, $args);
            } elseif (is_array($callback)) {
                $controller = new $callback[0];
                $request = new Request();
                return call_user_func_array([$controller, $callback[1]], array_merge([$request], $params));
            } else {
                throw new InvalidRouteConfigurationException('Invalid callback configuration');
            }
        } catch (InvalidRouteConfigurationException $th) {
            ExceptionHandler::handleException($th);
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

    private function checkAppKey()
    {
        try {
            if (empty(APP_KEY)) {
                throw new \Exception('La clave de la aplicación (APP_KEY) no está configurada. Genera una clave (<strong>php console key:generate</strong>) o escribe una en el archivo env.php.', 1800);
            }
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }
}
