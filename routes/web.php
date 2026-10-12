<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoController;

Route::get('/login', function () {
    return view('login'); // Asegúrate de que tu archivo se llame login.blade.php
})->name('login');

// 2. Ruta para procesar el inicio de sesión vía AJAX (Método POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Vista del menú protegida por autenticación
Route::get('/menu', function () {
    return view('menu');
})->middleware('auth')->name('menu');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login'); // cámbialo por la ruta de tu login
})->name('logout');

Route::prefix('almacen/tablas/{catalogo}')->name('catalogo.')->group(function () {
    Route::get('/',      [CatalogoController::class, 'index'])->name('index');
    Route::post('/',     [CatalogoController::class, 'store'])->name('store');
    Route::put('/{id}',  [CatalogoController::class, 'update'])->name('update');
    Route::delete('/{id}', [CatalogoController::class, 'destroy'])->name('destroy');
});