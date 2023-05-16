<?php

namespace App\Middleware;

use Lib\Http\Middleware\MiddlewareInterface;
use Lib\Http\Request;
use Lib\Http\Session;

use function Lib\Global\redirect;

class Authenticate implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        $request = new Request();

        if (!$request->isAuthenticated()) {
            Session::setFlash('error', 'You must be logged in to access this page.');
            redirect('/login');
        }

        return $next();
    }
}
