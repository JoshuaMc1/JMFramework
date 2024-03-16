<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Lib\Router\Route;

Route::setPrefix('/api');

Route::group([ApiAuthMiddleware::class], function () {
    Route::get('/user', [UserController::class, 'index'])
        ->name('api.user.index');
});
