<?php

namespace App\Middleware;

use Lib\Http\Middleware\MiddlewareInterface;

class Middleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        return $next();
    }
}
