<?php

namespace Lib\Http\Middleware;

use Exception;
use Lib\Exception\ExceptionHandler;
use Lib\Exception\RouteExceptions\CSRFTokenException;
use Lib\Http\CsrfTokenManager;
use Lib\Http\Middleware\Contracts\MiddlewareInterface;
use Lib\Http\Request;
use Lib\Route;

class CsrfMiddleware implements MiddlewareInterface
{
    protected $except = [
        '/api/*',
    ];

    public function handle(callable $next, Request $request)
    {
        try {
            if (!$request->isMethod('POST')) {
                return $next($request);
            }

            $currentRoute = $request->getPath();

            if (Route::shouldExcludeCsrfForRoute($currentRoute)) {
                return $next($request);
            }

            if (in_array($currentRoute, $this->except)) {
                return $next($request);
            }

            $submittedCsrfToken = $request->input('_token', '');

            if (!CsrfTokenManager::validateCsrfToken($submittedCsrfToken)) {
                throw new CSRFTokenException();
            }

            return $next($request);
        } catch (CSRFTokenException | Exception $e) {
            ExceptionHandler::handleException($e);
        }
    }
}
