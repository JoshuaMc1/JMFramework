<?php

use App\Middleware\ApiAuthMiddleware;
use Lib\Http\Auth;
use Lib\Route;

Route::group([ApiAuthMiddleware::class], function () {
    Route::get('/api/user', function () {
        return Auth::userAPI();
    });
});
