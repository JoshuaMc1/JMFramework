<?php

namespace App\Http\Middleware;

use Lib\Http\Middleware\MiddlewareInterface;
use Lib\Http\Request;

class Authenticate implements MiddlewareInterface
{
    public function handle(callable $next, Request $request)
    {
        if (!$request->isAuthenticated()) {
            redirect('/login');
        }

        return $next();
    }
}
