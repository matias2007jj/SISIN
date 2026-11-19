<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ContextoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas y Autenticación
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Sesión)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/menu', function () {
        return view('menu');
    })->name('menu');

    // Contexto (Solo superusuario: elegir con qué institución trabajar)
    Route::post('contexto/institucion', [ContextoController::class, 'institucion'])
        ->middleware('superusuario')
        ->name('contexto.institucion');

    // Almacén: Bienes (CRUD completo)
    Route::prefix('almacen/bienes')->name('bienes.')->group(function () {
        Route::get('/',        [BienController::class, 'index'])->name('index');
        Route::post('/',       [BienController::class, 'store'])->name('store');
        Route::put('/{id}',    [BienController::class, 'update'])->name('update');
        Route::delete('/{id}', [BienController::class, 'destroy'])->name('destroy');
    });

    // Almacén: Inventarios
    Route::prefix('almacen/inventarios')->name('inventarios.')->group(function () {
        Route::get('/',             [InventarioController::class, 'index'])->name('index');
        Route::get('/{inventario}', [InventarioController::class, 'show'])->name('show');
    });

    // Almacén: Tablas / Catálogos auxiliares
    Route::prefix('almacen/tablas/{catalogo}')->name('catalogo.')->group(function () {
        Route::get('/',        [CatalogoController::class, 'index'])->name('index');
        Route::post('/',       [CatalogoController::class, 'store'])->name('store');
        Route::put('/{id}',    [CatalogoController::class, 'update'])->name('update');
        Route::delete('/{id}', [CatalogoController::class, 'destroy'])->name('destroy');
    });

    // Usuarios del personal (Admin de institución o superusuario)
    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('administra-usuarios');

    // Instituciones (Alta, edición y baja: solo superusuario)
    Route::resource('instituciones', InstitucionController::class)
        ->parameters(['instituciones' => 'institucion'])
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('superusuario');

    Route::get('instituciones/{institucion}', [InstitucionController::class, 'show'])
        ->name('instituciones.show');

    // Programas de estudio de una institución
    Route::post('instituciones/{institucion}/programas', [InstitucionController::class, 'storePrograma'])
        ->name('instituciones.programas.store');

    // Personal asignado a una institución
    Route::prefix('instituciones/{institucion}/personal')->name('instituciones.personal.')->group(function () {
        Route::post('/',              [InstitucionController::class, 'storePersonal'])->name('store');
        Route::put('/{asignacion}',    [InstitucionController::class, 'updatePersonal'])->name('update');
        Route::delete('/{asignacion}', [InstitucionController::class, 'destroyPersonal'])->name('destroy');
    });

    // Personal general
    Route::resource('personal', PersonalController::class)
        ->parameters(['personal' => 'persona'])
        ->only(['index', 'store', 'update', 'destroy']);

});