<?php

namespace App\Http\Middleware;

use Lib\Http\Middleware\CsrfMiddleware as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
