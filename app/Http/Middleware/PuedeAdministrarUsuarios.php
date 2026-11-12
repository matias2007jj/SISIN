<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Deja pasar solo al superusuario y al administrador de una institución. */
class PuedeAdministrarUsuarios
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->puedeAdministrarUsuarios(), 403, 'No tiene permiso para gestionar usuarios.');

        return $next($request);
    }
}
