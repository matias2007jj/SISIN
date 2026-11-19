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
| Quién entra a cada una lo decide el middleware 'permiso:...'
| (la matriz de permisos por rol está en App\Support\Permisos).
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    // Mi cuenta: cualquier rol puede cambiar su propia contraseña
    Route::get('mi-cuenta', [CuentaController::class, 'edit'])->name('cuenta.edit');
    Route::put('mi-cuenta', [CuentaController::class, 'update'])->name('cuenta.update');

    // Contexto: programa de estudio con el que se trabaja (solo los que el usuario tiene asignados)
    Route::post('contexto/programa', [ContextoController::class, 'programa'])->name('contexto.programa');

    // Contexto: institución activa (solo superusuario)
    Route::post('contexto/institucion', [ContextoController::class, 'institucion'])
        ->middleware('superusuario')
        ->name('contexto.institucion');

    // Almacén: Bienes
    //   ver         -> todos los roles
    //   registrar   -> superusuario, director, coordinador, asistente
    //   inactivar   -> superusuario, director, coordinador
    Route::prefix('almacen/bienes')->name('bienes.')->middleware('permiso:bienes.ver')->group(function () {
        Route::get('/',        [BienController::class, 'index'])->name('index');
        Route::post('/',       [BienController::class, 'store'])->middleware('permiso:bienes.registrar')->name('store');
        Route::put('/{id}',    [BienController::class, 'update'])->middleware('permiso:bienes.registrar')->name('update');
        Route::delete('/{id}', [BienController::class, 'destroy'])->middleware('permiso:bienes.inactivar')->name('destroy');
    });

    // Almacén: Inventarios (consulta para todos los roles)
    Route::prefix('almacen/inventarios')->name('inventarios.')->middleware('permiso:inventarios.ver')->group(function () {
        Route::get('/',             [InventarioController::class, 'index'])->name('index');
        Route::get('/{inventario}', [InventarioController::class, 'show'])->name('show');
    });

    // Almacén: Tablas / Catálogos auxiliares
    // El permiso depende del catálogo y la acción (catalogo.sala.editar, ...): lo valida el controlador.
    Route::prefix('almacen/tablas/{catalogo}')->name('catalogo.')->middleware('permiso:catalogos.ver')->group(function () {
        Route::get('/',        [CatalogoController::class, 'index'])->name('index');
        Route::post('/',       [CatalogoController::class, 'store'])->name('store');
        Route::put('/{id}',    [CatalogoController::class, 'update'])->name('update');
        Route::delete('/{id}', [CatalogoController::class, 'destroy'])->name('destroy');
    });

    // Usuarios del personal (director de la institución o superusuario)
    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('permiso:usuarios.gestionar');

    // Instituciones (alta, edición y baja: solo superusuario)
    Route::resource('instituciones', InstitucionController::class)
        ->parameters(['instituciones' => 'institucion'])
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('superusuario');

    // Ficha de la institución: superusuario y director (todo); coordinador (solo lectura de sus programas)
    Route::get('instituciones/{institucion}', [InstitucionController::class, 'show'])
        ->middleware('permiso:institucion.ver')
        ->name('instituciones.show');

    // Programas y personal de la institución: solo superusuario y director
    Route::middleware('permiso:institucion.gestionar')->group(function () {
        Route::post('instituciones/{institucion}/programas', [InstitucionController::class, 'storePrograma'])
            ->name('instituciones.programas.store');

        Route::prefix('instituciones/{institucion}/personal')->name('instituciones.personal.')->group(function () {
            Route::post('/',               [InstitucionController::class, 'storePersonal'])->name('store');
            Route::put('/{asignacion}',    [InstitucionController::class, 'updatePersonal'])->name('update');
            Route::delete('/{asignacion}', [InstitucionController::class, 'destroyPersonal'])->name('destroy');
        });
    });

});
