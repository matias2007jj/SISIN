<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        $request->session()->forget('programa_estudio_id');

        return redirect()->route('menu');
    }

    public function programa(Request $request)
    {
        $datos = $request->validate([
            'programa_estudio_id' => ['required', 'integer'],
        ]);

        abort_unless(
            $request->user()->puedeUsarPrograma((int) $datos['programa_estudio_id']),
            403,
            'No tiene acceso a ese programa de estudio.'
        );

        $request->session()->put('programa_estudio_id', (int) $datos['programa_estudio_id']);

        return redirect()->back();
    }
}
