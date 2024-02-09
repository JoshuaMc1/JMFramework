<?php

namespace Lib\Router;

use Lib\Router\Contracts\RouteInterface;
use App\Http\Middleware\Middleware;
use Lib\Exception\ExceptionHandler;
use Lib\Router\Exceptions\{
    MiddlewareException,
    PageNotFoundException,
    InternalServerErrorException,
    InvalidRouteConfigurationException,
    MethodNotAllowedException
};
use Lib\Http\Request;

/**
 * Route Class
 *
 * This class is responsible for defining and managing routes in the application.
 * It allows you to define routes for various HTTP methods, apply middleware, and dispatch
 * incoming requests to the appropriate route handlers.
 */
class Route implements RouteInterface
{
    /**
     * The routes defined in the application.
     * 
     * @var array $routes
     */
    private static $routes = [];

    /**
     * The middleware defined in the application.
     * 
     * @var array $middlewares
     */
    private static $middlewares = [];

    /**
     * The default middleware defined in the application.
     * 
     * @var array $defaultMiddlewares
     */
    protected static $defaultMiddlewares = [];

    /**
     * The named routes defined in the application.
     * 
     * @var array $namedRoutes
     */
    private static $namedRoutes = [];

    /**
     * The prefix defined in the application.
     * 
     * @var string $prefix
     */
    private static $prefix = '';

    /**
     * Constants for HTTP methods.
     * 
     * @var string GET_METHOD, POST_METHOD, PUT_METHOD, PATCH_METHOD, DELETE_METHOD, OPTIONS_METHOD.
     */
    private const GET_METHOD = 'GET';
    private const POST_METHOD = 'POST';
    private const PUT_METHOD = 'PUT';
    private const PATCH_METHOD = 'PATCH';
    private const DELETE_METHOD = 'DELETE';
    private const OPTIONS_METHOD = 'OPTIONS';

    /**
     * Constructor
     * 
     * Initializes the class.
     */
    public function __construct()
    {
        error_reporting(E_ERROR);
        ini_set('display_errors', 0);
    }

    /**
     * Static method to define a GET route.
     * 
     * @param string $uri
     * @param mixed $callback
     * 
     * @return Route 
     */
    public static function get($uri, $callback): Route
    {
        self::addRoute(self::GET_METHOD, $uri, $callback);
        return new static();
    }

    /**
     * Static method to define a POST route.
     * 
     * @param string $uri
     * @param mixed $callback
     * 
     * @return Route
     */
    public static function post($uri, $callback): Route
    {
        self::addRoute(self::POST_METHOD, $uri, $callback);
        return new static();
    }

    /**
     * Static method to define a PUT route.
     * 
     * @param string $uri
     * @param mixed $callback
     * 
     * @return Route
     */
    public static function put($uri, $callback): Route
    {
        self::addRoute(self::PUT_METHOD, $uri, $callback);
        return new static();
    }

    /**
     * Static method to define a PATCH route.
     * 
     * @param string $uri
     * @param mixed $callback
     * 
     * @return Route
     */
    public static function patch($uri, $callback): Route
    {
        self::addRoute(self::PATCH_METHOD, $uri, $callback);
        return new static();
    }

    /**
     * Static method to define a DELETE route.
     * 
     * @param string $uri
     * @param mixed $callback
     * 
     * @return Route
     */
    public static function delete($uri, $callback): Route
    {
        self::addRoute(self::DELETE_METHOD, $uri, $callback);
        return new static();
    }

    /**
     * Static method to define a OPTIONS route.
     * 
     * @param string $uri
     * @param mixed $callback
     * 
     * @return Route
     */
    public static function options($uri, $callback): Route
    {
        self::addRoute(self::OPTIONS_METHOD, $uri, $callback);
        return new static();
    }

    /**
     * Static method to set the prefix for all routes.
     * 
     * @param string $prefix
     * 
     * @return void
     */
    public static function setPrefix($prefix): void
    {
        self::$prefix = $prefix;
    }

    /**
     * Static method to add middleware to all routes.
     * 
     * @param array $middlewares
     * 
     * @return Route
     */
    public static function middleware(array $middlewares = []): Route
    {
        try {
            $route = new static();
            $route::$middlewares = $middlewares;

            return $route;
        } catch (\Throwable  $th) {
            ExceptionHandler::handleException(new MiddlewareException($th->getMessage()));
        }
    }

    /**
     * Add middleware to the route.
     * 
     * @param array $middlewares
     * 
     * @return Route
     */
    public function addMiddlewareToRoute(array $middlewares = []): Route
    {
        $this::$middlewares = $middlewares;

        return $this;
    }

    /**
     * Check if the route should be excluded from CSRF protection.
     * 
     * @param string $uri
     * 
     * @return bool
     */
    public static function shouldExcludeCsrfForRoute(string $uri): bool
    {
        $excludedPrefixes = config('middleware.exclude_prefixes', []);

        foreach ($excludedPrefixes as $prefix) {
            if (strpos($uri, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load the default middleware configuration.
     * 
     * @return void
     */
    public static function loadMiddlewareConfig(): void
    {
        self::$defaultMiddlewares = config('middleware.default', []);
    }

    /**
     * Get the default middleware configuration.
     * 
     * @return array
     */
    public static function getDefaultMiddlewares(): array
    {
        return self::$defaultMiddlewares ?? [];
    }

    /**
     * Define a named route.
     * 
     * @param string $name
     * 
     * @return Route
     */
    public static function name(string $name): Route
    {
        $keys = array_keys(self::$routes);
        $lastAddedMethod = end($keys) ?? null;
        $lastAddedRoute = end(self::$routes[$lastAddedMethod]) ?? null;

        if ($lastAddedRoute) {
            $uri = $lastAddedRoute['uri'];
            self::$namedRoutes[$name] = $uri;
        }

        return new static();
    }

    /**
     * Define a group of routes.
     * 
     * @param array $middlewares
     * @param callable $callback
     * 
     * @return void
     */
    public static function group(array $middlewares, callable $callback): void
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

    /**
     * Dispatch the request to the appropriate route handler.
     * 
     * @return void
     */
    public static function dispatch(): void
    {
        try {
            self::loadMiddlewareConfig();

            $request = new Request();
            $method = $request->getMethod() ?? self::GET_METHOD;
            $uri = trim($_SERVER['REQUEST_URI'], '/');

            if ($request->isMethod('POST') && $request->getMethodOverride()) {
                $simulatedMethod = strtoupper($request->getMethodOverride());

                $allowedMethods = [
                    self::PUT_METHOD,
                    self::PATCH_METHOD,
                    self::DELETE_METHOD,
                    self::OPTIONS_METHOD
                ];

                (in_array($simulatedMethod, $allowedMethods)) ?
                    $method = $simulatedMethod :
                    throw new MethodNotAllowedException($simulatedMethod, $uri);
            }

            $routes = self::$routes[$method] ?? [];

            $match = self::match($uri, $routes, $method);

            if ($match === null) {
                throw new PageNotFoundException();
            }

            $defaultMiddlewares = self::getDefaultMiddlewares();
            $customMiddlewares = $match['middlewares'] ?? [];

            $middlewares = array_merge($customMiddlewares, $defaultMiddlewares);

            $middlewares = array_unique($middlewares);

            if (empty($middlewares)) {
                $middlewares = [Middleware::class];
            }

            $next = function () use ($match) {
                return self::execute($match);
            };

            $response = null;

            foreach ($middlewares as $middleware) {
                $middlewareInstance = new $middleware();
                $response = $middlewareInstance->handle($next, $request);
            }

            if (!$response) {
                $response = $next();
            }

            echo (is_array($response) || is_object($response)) ?
                response()->json($response)->send() : $response ?? '';
        } catch (PageNotFoundException | MethodNotAllowedException | \Throwable $e) {
            ExceptionHandler::handleException($e);
        } catch (InternalServerErrorException $e) {
            ExceptionHandler::handleException($e);
        }
    }

    /**
     * Match the requested URI to a route.
     * 
     * @param string $uri
     * @param array $routes
     * @param string $requestedMethod
     * 
     * @return array|null
     */
    private static function match(string $uri, array $routes, string $requestedMethod): ?array
    {
        foreach ($routes as $route => $callback) {
            $pattern = self::prepareRoutePattern($route);

            if (preg_match($pattern, $uri, $matches)) {
                $allowedMethods = $callback['allowed_methods'] ?? [];

                if (!empty($allowedMethods) && !in_array($requestedMethod, $allowedMethods)) {
                    throw new MethodNotAllowedException($requestedMethod, $uri);
                }

                return [
                    'callback' => $callback['callback'],
                    'params' => array_slice($matches, 1),
                    'middlewares' => $callback['middlewares'] ?? []
                ];
            }
        }

        return null;
    }

    /**
     * Execute the matched route.
     * 
     * @param array $match
     * 
     * @return mixed
     */
    private static function execute(array $match): mixed
    {
        try {
            $callback = $match['callback'];
            $params = $match['params'];

            $args = [];

            switch (true) {
                case is_callable($callback):
                    $args = self::resolveCallableArguments($callback);

                    break;
                case is_array($callback):
                    $controller = new $callback[0];
                    $request = new Request();
                    $args = array_merge([$request], $params);

                    return call_user_func_array([$controller, $callback[1]], $args);
                default:
                    throw new InvalidRouteConfigurationException(lang('invalid_callback_configuration'));
            }

            return call_user_func_array($callback, $args);
        } catch (InvalidRouteConfigurationException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Resolve the callable arguments.
     * 
     * @param mixed $callback
     * 
     * @return array
     */
    private static function resolveCallableArguments(array $callback): array
    {
        $reflector = is_array($callback)
            ? new \ReflectionMethod($callback[0], $callback[1])
            : new \ReflectionFunction($callback);

        $parameters = $reflector->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $args[] = ($parameter->getType() === Request::class) ? new Request() : null;
        }

        return $args;
    }

    /**
     * Add a route to the routes array.
     * 
     * @param string $method
     * @param string $uri
     * @param mixed $callback
     * 
     * @return void
     */
    private static function addRoute(string $method, string $uri, mixed $callback): void
    {
        $uri = self::$prefix . $uri;

        self::$routes[$method][trim($uri, '/')] = [
            'callback' => $callback,
            'middlewares' => self::$middlewares,
            'uri' => $uri,
        ];
    }

    /**
     * Get the named routes.
     * 
     * @return array
     */
    public static function getNamedRoutes(): array
    {
        return self::$namedRoutes;
    }

    /**
     * Prepare the route pattern.
     * 
     * @param string $route
     * 
     * @return string
     */
    private static function prepareRoutePattern(string $route): string
    {
        $pattern = preg_replace('#:[a-zA-Z]+#', '([^/]+)', $route);
        return "#^$pattern$#";
    }
}
