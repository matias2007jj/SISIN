<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\ProgramaEstudio;
use Illuminate\Http\Request;

class ProgramaEstudioController extends Controller
{
    public function index()
    {
        $programas = ProgramaEstudio::with(['institucion', 'coordinador'])
            ->orderBy('nombre')
            ->get();
        $instituciones = Institucion::orderBy('nombre')->get();

        return view('organizacion.programas', compact('programas', 'instituciones'));
    }

    public function store(Request $request)
    {
        ProgramaEstudio::create($this->validar($request));

        return redirect()->route('programas.index')->with('success', 'Programa de estudio registrado correctamente');
    }

    public function update(Request $request, ProgramaEstudio $programa)
    {
        $programa->update($this->validar($request));

        return redirect()->route('programas.index')->with('success', 'Programa de estudio actualizado correctamente');
    }

    public function destroy(ProgramaEstudio $programa)
    {
        if ($programa->salas()->exists() || $programa->inventarios()->exists() || $programa->usuarios()->exists()) {
            return redirect()->route('programas.index')
                ->with('error', 'No se puede eliminar: el programa tiene salas, inventarios o usuarios asociados.');
        }

        $programa->asignaciones()->delete();
        $programa->delete();

        return redirect()->route('programas.index')->with('success', 'Programa de estudio eliminado correctamente');
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'institucion_id' => ['required', 'exists:instituciones,id'],
            'nombre'         => ['required', 'string', 'max:100'],
            'abreviatura'    => ['nullable', 'string', 'max:30'],
            'observacion'    => ['nullable', 'string', 'max:150'],
        ]);

        $datos['nombre']      = mb_strtoupper(trim($datos['nombre']));
        $datos['abreviatura'] = isset($datos['abreviatura']) ? mb_strtoupper(trim($datos['abreviatura'])) : null;
        $datos['activo']      = $request->boolean('activo');

        return $datos;
    }
}
