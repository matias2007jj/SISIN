<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso en rutas:  ->middleware('permiso:bienes.registrar')
 * Con varios permisos basta uno:  ->middleware('permiso:bienes.ver,inventarios.ver')
 * Los permisos de cada rol están en App\Support\Permisos.
 */
class TienePermiso
{
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        $usuario = $request->user();

        $permitido = $usuario && collect($permisos)->contains(fn ($p) => $usuario->puede($p));

        abort_unless($permitido, 403, 'Su rol no tiene permiso para realizar esta acción.');

        return $next($request);
    }
}
