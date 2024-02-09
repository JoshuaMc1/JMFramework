<?php

namespace App\Http\Middleware;

use Lib\Http\Middleware\Contracts\MiddlewareInterface;
use Lib\Http\Request;

/**
 * Class Authenticate
 * 
 * @package App\Http\Middleware
 * 
 * this middleware checks if the user is logged in and redirects to the login page
 */
class Authenticate implements MiddlewareInterface
{
    /**
     * The function checks if the request is authenticated and redirects to the login page if not,
     * otherwise it calls the next middleware.
     * 
     * @param callable next The `` parameter is a callable that represents the next middleware or
     * handler in the request/response cycle. It is responsible for processing the request and
     * returning a response.
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request made to the server. It contains information about the request, such
     * as the request method, headers, and request body.
     * 
     * @return mixed the result of calling the `` callable.
     */
    public function handle(callable $next, Request $request): mixed
    {
        if (!$request->isAuthenticated()) {
            redirect('/login');
        }

        return $next();
    }
}
