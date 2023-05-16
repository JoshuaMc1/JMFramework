<?php

use App\Controllers\PruebaController;
use App\Middleware\Authenticate;
use App\Models\User;
use Lib\Http\Auth;
use Lib\Route;
use Lib\Support\Hash;

Route::get('/', [PruebaController::class, 'index']);
Route::get('/login', function () {
    $email = "joshua@me.com";
    $password = "password";

    if (Auth::attempt($email, $password)) {
        return 'Logged in';
    }

    return 'Not logged in';
});

Route::get('/register', function () {
    $user = [
        'name' => 'Josh',
        'email' => 'joshua@me.com',
        'password' => Hash::make('password'),
    ];

    $newUser = new User();
    $newUser->create($user);

    return $newUser;
});

Route::get('/logout', function () {
    Auth::logout();
});

Route::group([Authenticate::class], function () {
    Route::get('/prueba', [PruebaController::class, 'index']);
    Route::get('/user', function () {
        return Auth::user();
    });
});

Route::dispatch();
