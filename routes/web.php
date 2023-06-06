<?php

use App\Http\Controllers\WelcomeController;
use Lib\Route;

Route::get('/', [WelcomeController::class, 'welcome']);
