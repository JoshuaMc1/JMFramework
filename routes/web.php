<?php

use App\Http\Controllers\WelcomeController;
use Lib\Router\Route;

Route::get('/', [WelcomeController::class, 'welcome'])
    ->name('welcome.index');
