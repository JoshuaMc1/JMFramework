<?php

namespace Lib\Kernel;

use Lib\Exception\AppExceptions\KeyNotFoundException;
use Lib\Exception\ExceptionHandler;
use Lib\Exception\KernelExceptions\RouteFileNotFoundException;
use Lib\Http\{
    Cors,
    CsrfTokenManager,
    Request
};
use Lib\Kernel\Contracts\KernelInterface;
use Lib\Router\Route;
use Lib\Support\Env;

/**
 * Class BaseKernel
 * 
 * This class is used to bootstrap the application.
 * 
 * @package Lib\Kernel
 */
class BaseKernel implements KernelInterface
{
    /**
     * Bootstraps the application.
     *
     * @return void
     */
    public static function boot()
    {
        self::initializeGlobals();
        self::initialize();
        self::checkAppKey();
        self::handleCors();
        self::generateCsrfToken();
        self::registerRoutes();
    }

    /**
     * Initializes global variables.
     *
     * @return void
     */
    private static function initializeGlobals()
    {
        require_once __DIR__ . '/../Global/Global.php';
    }

    /**
     * Initializes the environment.
     *
     * @return void
     */
    private static function initialize()
    {
        Env::load();
    }

    /**
     * Handles Cross-Origin Resource Sharing (CORS).
     *
     * @return void
     */
    private static function handleCors()
    {
        Cors::handleCors();
    }

    /**
     * Generates CSRF tokens.
     *
     * @return void
     */
    private static function generateCsrfToken()
    {
        CsrfTokenManager::generateToken();
    }

    /**
     * Registers the routes for the application.
     *
     * @return void
     */
    private static function registerRoutes()
    {
        $routes = self::getRoutes();
        self::requireRoutes($routes);

        $namedRoutes = Route::getNamedRoutes();
        Request::setNamedRoutes($namedRoutes);

        Route::dispatch();
    }

    /**
     * Returns an array of routes.
     *
     * @return array
     */
    private static function getRoutes()
    {
        return [
            routes_path() . '/web.php',
            routes_path() . '/api.php',
        ];
    }

    /**
     * Requires the routes.
     *
     * @param array $routes
     * @return void
     */
    private static function requireRoutes($routes)
    {
        try {
            foreach ($routes as $route) {
                if (!file_exists($route)) {
                    throw new RouteFileNotFoundException($route);
                }

                require_once $route;
            }
        } catch (\Throwable $e) {
            ExceptionHandler::handleException($e);
        }
    }

    /**
     * Checks if the application key is set.
     * 
     * @throws KeyNotFoundException
     * 
     * @return void
     */
    private static function checkAppKey()
    {
        try {
            if (!config('app.key')) {
                throw new KeyNotFoundException();
            }
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }
}
