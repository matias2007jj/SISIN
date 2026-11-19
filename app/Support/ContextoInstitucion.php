<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;


class ContextoInstitucion
{
    public static function id(): ?int
    {
        $usuario = Auth::user();

        if (! $usuario instanceof Usuario) {
            return null;                     
        }

        if ($usuario->institucion_id) {
            return (int) $usuario->institucion_id;      
        }

        if ($usuario->esSuperusuario()) {
            $elegida = session('institucion_id');       
            return $elegida ? (int) $elegida : null;
        }

        return -1;   
    }

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

    public static function reglaPrograma(): Exists
    {
        $regla = Rule::exists('programas_estudio', 'id')->whereNull('deleted_at');

        if (($id = self::id()) !== null) {
            $regla->where('institucion_id', $id);
        }

        return $regla;
    }
}
