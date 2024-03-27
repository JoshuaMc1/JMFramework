<?php

use App\Http\Middleware\ApiAuthMiddleware;
use Lib\Router\Route;

Route::setPrefix('/api');

Route::middleware([ApiAuthMiddleware::class])->group(function () {
    Route::get(
        '/user',
        fn () =>
        response()->json([
            'data' => auth()->user('api'),
        ])->send()
    );
});
