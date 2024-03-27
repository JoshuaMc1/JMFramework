<?php

namespace Lib\Http\Middleware;

use Exception;
use Lib\Exception\ExceptionHandler;
use Lib\Http\CsrfTokenManager;
use Lib\Http\Middleware\Contracts\MiddlewareInterface;
use Lib\Http\Request;
use Lib\Router\Exceptions\CSRFTokenException;
use Lib\Router\Route;

class CsrfMiddleware implements MiddlewareInterface
{
    protected $except = [
        '/api/*',
    ];

    public function handle(callable $next, Request $request)
    {
        try {
            if ($request->isMethod('GET')) {
                return $next($request);
            }

            if (Route::shouldExcludeCsrfForRoute($request->getPath())) {
                return $next($request);
            }

            if (in_array($request->getPath(), $this->except)) {
                return $next($request);
            }

            if (!CsrfTokenManager::validateCsrfToken($request->input('_token', ''))) {
                throw new CSRFTokenException();
            }

            return $next($request);
        } catch (CSRFTokenException | Exception $e) {
            ExceptionHandler::handleException($e);
        }
    }
}
