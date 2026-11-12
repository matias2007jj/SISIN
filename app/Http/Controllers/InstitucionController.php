<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstitucionController extends Controller
{
    public function index()
    {
        $instituciones = Institucion::withCount('programas')->orderBy('nombre')->get();

        return view('organizacion.instituciones', compact('instituciones'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Institucion::create($datos);

        return redirect()->route('instituciones.index')->with('success', 'Institución registrada correctamente');
    }

    public function update(Request $request, Institucion $institucion)
    {
        $institucion->update($this->validar($request, $institucion->id));

        return redirect()->route('instituciones.index')->with('success', 'Institución actualizada correctamente');
    }

    public function destroy(Institucion $institucion)
    {
        if ($institucion->programas()->exists()) {
            return redirect()->route('instituciones.index')
                ->with('error', 'No se puede eliminar: la institución tiene programas de estudio registrados.');
        }

        $institucion->delete();

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        $datos = $request->validate([
            'ruc'       => ['required', 'digits:11', Rule::unique('instituciones', 'ruc')->ignore($id)],
            'nombre'    => ['required', 'string', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:150'],
        ]);

        $datos['nombre']    = mb_strtoupper(trim($datos['nombre']));
        $datos['direccion'] = isset($datos['direccion']) ? trim($datos['direccion']) : null;

        return $datos;
    }
}
