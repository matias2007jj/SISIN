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

        return redirect()->route('menu');
    }
}