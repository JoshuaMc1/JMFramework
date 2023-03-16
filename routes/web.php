<?php

use Lib\Route;
use App\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'welcome']);

Route::dispatch();
