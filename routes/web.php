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

    // Solo superusuario: elegir con qué institución trabajar
    Route::post('contexto/institucion', [ContextoController::class, 'institucion'])
        ->middleware('superusuario')
        ->name('contexto.institucion');

    // Inventario: cada usuario ve únicamente lo de su institución (lo garantiza InstitucionScope)
    Route::get('almacen/bienes', [BienController::class, 'index'])->name('bienes.index');
    Route::get('almacen/inventarios', [InventarioController::class, 'index'])->name('inventarios.index');
    Route::get('almacen/inventarios/{inventario}', [InventarioController::class, 'show'])->name('inventarios.show');

    Route::prefix('almacen/tablas/{catalogo}')->name('catalogo.')->group(function () {
        Route::get('/',        [CatalogoController::class, 'index'])->name('index');
        Route::post('/',       [CatalogoController::class, 'store'])->name('store');
        Route::put('/{id}',    [CatalogoController::class, 'update'])->name('update');
        Route::delete('/{id}', [CatalogoController::class, 'destroy'])->name('destroy');
    });

    // Usuarios del personal: los gestiona el administrador de cada institución (o el superusuario)
    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('administra-usuarios');

    Route::prefix('organizacion')->group(function () {
        // Alta, edición y baja de instituciones: solo superusuario
        Route::resource('instituciones', InstitucionController::class)
            ->parameters(['instituciones' => 'institucion'])
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('superusuario');

        // La ficha la puede abrir su propia institución (el scope devuelve 404 para otras)
        Route::get('instituciones/{institucion}', [InstitucionController::class, 'show'])
            ->name('instituciones.show');

        // Programas de estudio de una institución
        Route::post('instituciones/{institucion}/programas', [InstitucionController::class, 'storePrograma'])
            ->name('instituciones.programas.store');

        // Personal de una institución (se gestiona desde la ficha de la institución)
        Route::prefix('instituciones/{institucion}/personal')->name('instituciones.personal.')->group(function () {
            Route::post('/',              [InstitucionController::class, 'storePersonal'])->name('store');
            Route::put('/{asignacion}',    [InstitucionController::class, 'updatePersonal'])->name('update');
            Route::delete('/{asignacion}', [InstitucionController::class, 'destroyPersonal'])->name('destroy');
        });

        Route::resource('personal', PersonalController::class)
            ->parameters(['personal' => 'persona'])
            ->only(['index', 'store', 'update', 'destroy']);
    });

});
