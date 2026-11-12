<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Personal;
use App\Models\ProgramaEstudio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PersonalController extends Controller
{
    public function index()
    {
        $personal = Personal::with('asignaciones.programaEstudio')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $programas = ProgramaEstudio::with('institucion')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $cargos = Asignacion::CARGOS;

        return view('organizacion.personal', compact('personal', 'programas', 'cargos'));
    }

    public function store(Request $request)
    {
        [$datos, $asignaciones] = $this->validar($request);

        DB::transaction(function () use ($datos, $asignaciones) {
            $persona = Personal::create($datos);
            $this->sincronizar($persona, $asignaciones);
        });

        return redirect()->route('personal.index')->with('success', 'Personal registrado correctamente');
    }

    public function update(Request $request, Personal $persona)
    {
        [$datos, $asignaciones] = $this->validar($request, $persona->id);

        DB::transaction(function () use ($persona, $datos, $asignaciones) {
            $persona->update($datos);
            $this->sincronizar($persona, $asignaciones);
        });

        return redirect()->route('personal.index')->with('success', 'Personal actualizado correctamente');
    }

    public function destroy(Personal $persona)
    {
        if (Usuario::where('personal_id', $persona->id)->exists()) {
            return redirect()->route('personal.index')
                ->with('error', 'No se puede eliminar: esta persona tiene un usuario del sistema asociado.');
        }

        DB::transaction(function () use ($persona) {
            $persona->asignaciones()->delete();
            ProgramaEstudio::where('coordinador_id', $persona->id)->update(['coordinador_id' => null]);
            $persona->delete();
        });

        return redirect()->route('personal.index')->with('success', 'Personal eliminado correctamente');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        $validado = $request->validate([
            'apellidos' => ['required', 'string', 'max:60'],
            'nombres'   => ['required', 'string', 'max:60'],
            'dni'       => ['required', 'digits:8', Rule::unique('personal', 'dni')->ignore($id)],
            'celular'   => ['nullable', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:150'],

            'asignaciones'                       => ['nullable', 'array'],
            'asignaciones.*.cargo'               => ['required', Rule::in(Asignacion::CARGOS)],
            'asignaciones.*.programa_estudio_id' => ['required', 'exists:programas_estudio,id'],
        ]);

        $asignaciones = collect($validado['asignaciones'] ?? [])
            ->map(fn ($a) => [
                'cargo'               => $a['cargo'],
                'programa_estudio_id' => (int) $a['programa_estudio_id'],
            ])
            ->unique(fn ($a) => $a['cargo'] . '-' . $a['programa_estudio_id'])
            ->values()
            ->all();

        unset($validado['asignaciones']);

        $validado['apellidos'] = mb_strtoupper(trim($validado['apellidos']));
        $validado['nombres']   = mb_strtoupper(trim($validado['nombres']));

        return [$validado, $asignaciones];
    }

    /**
     * Reemplaza las asignaciones de la persona y mantiene al día el
     * coordinador_id de cada programa de estudio.
     */
    private function sincronizar(Personal $persona, array $asignaciones): void
    {
        // Programas donde era coordinador y ya no lo será
        $coordina = collect($asignaciones)
            ->where('cargo', 'COORDINADOR')
            ->pluck('programa_estudio_id')
            ->all();

        ProgramaEstudio::where('coordinador_id', $persona->id)
            ->whereNotIn('id', $coordina)
            ->update(['coordinador_id' => null]);

        $persona->asignaciones()->delete();

        foreach ($asignaciones as $a) {
            $persona->asignaciones()->create($a);
        }

        if ($coordina) {
            ProgramaEstudio::whereIn('id', $coordina)->update(['coordinador_id' => $persona->id]);
        }
    }
}
