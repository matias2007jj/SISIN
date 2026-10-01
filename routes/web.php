<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', function () {
    return view('login'); // Asegúrate de que tu archivo se llame login.blade.php
})->name('login');

// 2. Ruta para procesar el inicio de sesión vía AJAX (Método POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Vista del menú protegida por autenticación
Route::get('/menu', function () {
    return view('menu');
})->middleware('auth')->name('menu');