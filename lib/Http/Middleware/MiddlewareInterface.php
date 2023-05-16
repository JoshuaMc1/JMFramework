<?php

namespace Lib\Http\Middleware;

interface MiddlewareInterface
{
    public function handle(callable $next);
}
