<?php

namespace App\Http\Middleware;

use Lib\Http\Middleware\MiddlewareInterface;
use Lib\Http\Request;

/**
 * Class Middleware
 * 
 * @package App\Http\Middleware
 * 
 * this class is used to wrap a middleware, so it can be used in a route
 */
class Middleware implements MiddlewareInterface
{
    /**
     * The function takes a callable and a request object as parameters and returns the result of
     * calling the callable with no arguments.
     * 
     * @param callable next The `` parameter is a callable function that represents the next
     * middleware or handler in the request/response cycle. It is responsible for processing the
     * request and returning a response.
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request made to the server. It contains information such as the request
     * method, headers, and body.
     * 
     * @return mixed the result of calling the `` callable.
     */
    public function handle(callable $next, Request $request): mixed
    {
        return $next();
    }
}
