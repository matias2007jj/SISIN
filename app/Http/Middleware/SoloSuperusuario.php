<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloSuperusuario
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->esSuperusuario(), 403, 'Solo el superusuario puede realizar esta acción.');

        return $next($request);
    }
}
