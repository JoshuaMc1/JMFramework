<?php

namespace Lib\Http\Middleware\Contracts;

use Lib\Http\Request;

/**
 * Interface MiddlewareInterface
 * 
 * defines the contract that middleware classes must implement.
 */
interface MiddlewareInterface
{
    /**
     * Handle the incoming request and optionally pass control to the next middleware.
     *
     * @param callable $next   The next middleware or request handler to be called.
     * @param Request $request The incoming HTTP request.
     *
     * @return mixed The response or result of processing the request.
     */
    public function handle(callable $next, Request $request);
}
