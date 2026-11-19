<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * Responde una sola pregunta: ¿a qué institución está limitada la sesión actual?
 *   - int  => solo ve datos de esa institución
 *   - null => sin límite (superusuario viendo "Todas")
 */
class ContextoInstitucion
{
    public static function id(): ?int
    {
        $usuario = Auth::user();

        if (! $usuario instanceof Usuario) {
            return null;                       // consola, seeders, login en curso
        }

        if ($usuario->institucion_id) {
            return (int) $usuario->institucion_id;      // fijo: no se puede cambiar
        }

        if ($usuario->esSuperusuario()) {
            $elegida = session('institucion_id');       // la que eligió en el selector
            return $elegida ? (int) $elegida : null;
        }

        return -1;   // usuario sin institución y sin ser superusuario: no ve nada (falla cerrado)
    }

    /**
     * Programa de estudio con el que está trabajando ahora la sesión.
     * Siempre es uno al que el usuario tiene acceso: si el guardado en sesión ya no es válido
     * (cambió de institución, le quitaron el acceso, etc.) se vuelve al primero permitido.
     */
    public static function programaId(): ?int
    {
        $usuario = Auth::user();

        if (! $usuario instanceof Usuario) {
            return null;
        }

        $permitidos = $usuario->idsProgramasPermitidos();
        $guardado   = (int) session('programa_estudio_id');

        if ($guardado && in_array($guardado, $permitidos, true)) {
            return $guardado;
        }

        $nuevo = $permitidos[0] ?? null;

        $nuevo
            ? session(['programa_estudio_id' => $nuevo])
            : session()->forget('programa_estudio_id');

        return $nuevo;
    }

    /** Regla de validación: el programa debe existir y pertenecer a la institución activa. */
    public static function reglaPrograma(): Exists
    {
        $regla = Rule::exists('programas_estudio', 'id')->whereNull('deleted_at');

        if (($id = self::id()) !== null) {
            $regla->where('institucion_id', $id);
        }

        return $regla;
    }
}
