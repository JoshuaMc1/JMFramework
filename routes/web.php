<?php

use App\Controllers\AuthorizationTestController;
use App\Controllers\WelcomeController;
use Lib\Route;

Route::get('/', [WelcomeController::class, 'welcome']);

Route::get('/test', [AuthorizationTestController::class, 'test']);
