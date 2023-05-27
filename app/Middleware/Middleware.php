<?php

namespace App\Middleware;

use Lib\Http\Middleware\MiddlewareInterface;
use Lib\Http\Request;

class Middleware implements MiddlewareInterface
{
    public function handle(callable $next, Request $request)
    {
        return $next();
    }
}
