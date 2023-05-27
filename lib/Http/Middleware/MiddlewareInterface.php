<?php

namespace Lib\Http\Middleware;

use Lib\Http\Request;

interface MiddlewareInterface
{
    public function handle(callable $next, Request $request);
}
