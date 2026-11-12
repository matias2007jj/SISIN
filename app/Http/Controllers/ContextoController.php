<?php

namespace App\Http\Controllers;

use App\Models\ProgramaEstudio;
use App\Models\Scopes\InstitucionScope;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** El superusuario "entra" a una institución para ver y gestionar su inventario. */
class ContextoController extends Controller
{
    public function institucion(Request $request)
    {
        $datos = $request->validate([
            'institucion_id' => ['nullable', Rule::exists('instituciones', 'id')->whereNull('deleted_at')],
        ]);

        if (empty($datos['institucion_id'])) {
            $request->session()->forget('institucion_id');
        } else {
            $request->session()->put('institucion_id', (int) $datos['institucion_id']);
        }

        // El programa activo debe ser de la institución elegida
        $programa = ProgramaEstudio::withoutGlobalScope(InstitucionScope::class)
            ->when(! empty($datos['institucion_id']), fn ($q) => $q->where('institucion_id', $datos['institucion_id']))
            ->where('activo', true)
            ->orderBy('nombre')
            ->first();

        $programa
            ? $request->session()->put('programa_estudio_id', $programa->id)
            : $request->session()->forget('programa_estudio_id');

        return redirect()->route('menu');
    }
}
