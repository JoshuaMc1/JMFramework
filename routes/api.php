<?php

use App\Http\Middleware\ApiAuthMiddleware;
use Lib\Http\Auth;
use Lib\Route;

Route::setPrefix('/api');

Route::group([ApiAuthMiddleware::class], function () {
    Route::get('/user', function () {
        return Auth::userAPI();
    });
});
