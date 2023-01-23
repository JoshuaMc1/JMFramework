<?php

use Lib\Route;
use App\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/contacto', function () {
    return 'Hola desde la pagina de contacto';
});

Route::get('courses/prueba', function () {
    return 'Curso de prueba';
});

Route::get('/courses/:id', [HomeController::class, 'courses']);

Route::dispatch();
