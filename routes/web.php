<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ContextoController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MenuController;
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

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    Route::middleware('superusuario')->group(function () {
        Route::get('mi-cuenta', [CuentaController::class, 'edit'])->name('cuenta.edit');
        Route::put('mi-cuenta', [CuentaController::class, 'update'])->name('cuenta.update');
    });

    

    Route::post('contexto/institucion', [ContextoController::class, 'institucion'])
        ->middleware('superusuario')
        ->name('contexto.institucion');

    Route::prefix('almacen/bienes')->name('bienes.')->middleware('permiso:bienes.ver')->group(function () {
        Route::get('/',                [BienController::class, 'index'])->name('index');
        Route::post('/',               [BienController::class, 'store'])->middleware('permiso:bienes.registrar')->name('store');
        Route::post('/importar-excel', [BienController::class, 'importarExcel'])->middleware('permiso:bienes.registrar')->name('importar.excel');
        Route::get('/plantilla-excel', [BienController::class, 'plantillaExcel'])->name('plantilla.excel');
        Route::get('/exportar-excel',  [BienController::class, 'exportarExcel'])->name('exportar.excel');
        Route::put('/{id}',            [BienController::class, 'update'])->middleware('permiso:bienes.registrar')->name('update');
        Route::delete('/{id}',         [BienController::class, 'destroy'])->middleware('permiso:bienes.inactivar')->name('destroy');
    });

    Route::prefix('almacen/inventarios')->name('inventarios.')->middleware('permiso:inventarios.ver')->group(function () {
        Route::get('/',             [InventarioController::class, 'index'])->name('index');
        Route::get('/{inventario}', [InventarioController::class, 'show'])->name('show');
    });

    Route::prefix('almacen/tablas/{catalogo}')->name('catalogo.')->middleware('permiso:catalogos.ver')->group(function () {
        Route::get('/',               [CatalogoController::class, 'index'])->name('index');
        Route::post('/',              [CatalogoController::class, 'store'])->name('store');
        Route::put('/{id}',           [CatalogoController::class, 'update'])->name('update');
        Route::delete('/{id}',        [CatalogoController::class, 'destroy'])->name('destroy');

        Route::post('/importar-excel', [CatalogoController::class, 'importarExcel'])->name('importar.excel');
        Route::get('/plantilla-excel', [CatalogoController::class, 'plantillaExcel'])->name('plantilla.excel');
        Route::get('/exportar-excel',  [CatalogoController::class, 'exportarExcel'])->name('exportar.excel');
        Route::get('/exportar-pdf',    [CatalogoController::class, 'exportarPdf'])->name('exportar.pdf');
    });

    Route::prefix('usuarios')->name('usuarios.')->middleware('permiso:usuarios.gestionar')->group(function () {
        Route::post('importar-excel', [UsuarioController::class, 'importarExcel'])->name('importar.excel');
        Route::get('plantilla-excel', [UsuarioController::class, 'plantillaExcel'])->name('plantilla.excel');
        Route::get('exportar-excel',  [UsuarioController::class, 'exportarExcel'])->name('exportar.excel');
        Route::get('exportar-pdf',    [UsuarioController::class, 'exportarPdf'])->name('exportar.pdf');
    });

    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('permiso:usuarios.gestionar');

    Route::resource('instituciones', InstitucionController::class)
        ->parameters(['instituciones' => 'institucion'])
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('superusuario');

    Route::get('instituciones/{institucion}', [InstitucionController::class, 'show'])
        ->middleware('permiso:institucion.ver')
        ->name('instituciones.show');

    Route::middleware('permiso:institucion.gestionar')->group(function () {
        Route::post('instituciones/{institucion}/programas', [InstitucionController::class, 'storePrograma'])
            ->name('instituciones.programas.store');
        Route::put('instituciones/{institucion}/programas/{programa}', [InstitucionController::class, 'updatePrograma'])
            ->name('instituciones.programas.update');
        Route::delete('instituciones/{institucion}/programas/{programa}', [InstitucionController::class, 'destroyPrograma'])
            ->name('instituciones.programas.destroy');
    });

});