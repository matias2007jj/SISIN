<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoController;

Route::get('/', function () {
    return redirect()->route('menu');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/acceso', function () {
    return redirect()->route('login');
})->name('acceso.index');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::match(['get', 'post'], '/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::get('/salir', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('salir.index');

Route::middleware(['auth'])->group(function () {

    Route::get('/menu', function () {
        return view('menu');
    })->name('menu');

    Route::prefix('almacen/tablas/{catalogo}')->name('catalogo.')->group(function () {
        Route::get('/',        [CatalogoController::class, 'index'])->name('index');
        Route::post('/',       [CatalogoController::class, 'store'])->name('store');
        Route::put('/{id}',    [CatalogoController::class, 'update'])->name('update');
        Route::delete('/{id}', [CatalogoController::class, 'destroy'])->name('destroy');
    });

});