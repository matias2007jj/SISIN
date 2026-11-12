<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Institucion;
use App\Models\Personal;
use App\Models\ProgramaEstudio;
use App\Models\Scopes\InstitucionScope;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstitucionController extends Controller
{
    public function index()
    {
        $instituciones = Institucion::withCount('programas')->orderBy('nombre')->get();

        return view('organizacion.instituciones', compact('instituciones'));
    }

    /** Ficha de la institución: su personal con programa de estudio y cargo. */
    public function show(Institucion $institucion)
    {
        $asignaciones = Asignacion::with(['personal', 'programaEstudio'])
            ->whereHas('programaEstudio', fn ($q) => $q->where('institucion_id', $institucion->id))
            ->whereHas('personal')
            ->get()
            ->sortBy(fn ($a) => $a->personal->apellidos . ' ' . $a->personal->nombres)
            ->values();

        $programas = $institucion->programas()->orderBy('nombre')->get();
        $cargos    = Asignacion::CARGOS;

        return view('organizacion.institucion', compact('institucion', 'asignaciones', 'programas', 'cargos'));
    }

    /**
     * Crea la institución Y su usuario de acceso en una sola transacción:
     * o se crean las dos cosas o no se crea ninguna.
     */
    public function store(Request $request)
    {
        $datos  = $this->validar($request);
        $acceso = $request->validate([
            'username' => ['nullable', 'string', 'max:15', 'regex:/^[A-Za-z0-9._-]+$/'],
            'password' => ['nullable', 'string', 'min:6', 'max:60'],
        ]);

        $username = $acceso['username'] ?? $datos['ruc'];       // por defecto, el RUC
        $clave    = $acceso['password'] ?? Str::password(10, symbols: false);

        if (Usuario::withTrashed()->where('username', $username)->exists()) {
            return redirect()->back()->withInput()
                ->withErrors(['username' => 'Ese nombre de usuario ya está en uso.']);
        }

        DB::transaction(function () use ($datos, $username, $clave) {
            $institucion = Institucion::create($datos);

            Usuario::create([
                'username'       => $username,
                'password'       => $clave,                       // el cast 'hashed' lo encripta
                'institucion_id' => $institucion->id,
                'tipo_usuario'   => Usuario::ADMIN_INSTITUCION,
                'activo'         => true,
            ]);
        });

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución registrada y usuario de acceso creado correctamente')
            ->with('credenciales', ['usuario' => $username, 'clave' => $clave]);   // se muestran una sola vez
    }

    public function update(Request $request, Institucion $institucion)
    {
        $institucion->update($this->validar($request, $institucion->id));

        return redirect()->back()->with('success', 'Institución actualizada correctamente');
    }

    public function destroy(Institucion $institucion)
    {
        if ($institucion->programas()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar: la institución tiene programas de estudio y personal asignado.');
        }

        DB::transaction(function () use ($institucion) {
            $institucion->usuarios()->delete();      // sus usuarios dejan de poder entrar
            $institucion->delete();
        });

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente');
    }

    /* ----------------------- Programas de la institución ---------------------- */

    public function storePrograma(Request $request, Institucion $institucion)
    {
        $datos = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'abreviatura' => ['nullable', 'string', 'max:30'],
        ]);

        $nombre = mb_strtoupper(trim($datos['nombre']));

        if ($institucion->programas()->where('nombre', $nombre)->exists()) {
            return redirect()->back()->with('error', 'Ese programa de estudio ya está registrado en la institución.');
        }

        $institucion->programas()->create([
            'nombre'      => $nombre,
            'abreviatura' => isset($datos['abreviatura']) ? mb_strtoupper(trim($datos['abreviatura'])) : null,
            'activo'      => true,
        ]);

        return redirect()->back()->with('success', 'Programa de estudio registrado correctamente');
    }

    /* ----------------------- Personal de la institución ----------------------- */

    public function storePersonal(Request $request, Institucion $institucion)
    {
        $datos = $request->validate($this->reglasPersona($institucion));

        DB::transaction(function () use ($datos, $institucion) {
            $persona = Personal::withoutGlobalScope(InstitucionScope::class)->withTrashed()->where('dni', $datos['dni'])->first();

            if ($persona) {
                // Ya existe (quizá en otra institución): se reutiliza y se actualizan sus datos
                if ($persona->trashed()) {
                    $persona->restore();
                }
                $persona->update($this->datosPersona($datos));
            } else {
                $persona = Personal::create($this->datosPersona($datos));
            }

            $programa = ProgramaEstudio::findOrFail($datos['programa_estudio_id']);

            $asignacion = Asignacion::firstOrCreate([
                'personal_id'         => $persona->id,
                'programa_estudio_id' => $programa->id,
                'cargo'               => $datos['cargo'],
            ]);

            $this->fijarCoordinacion($asignacion);
        });

        return redirect()->back()->with('success', 'Personal registrado en la institución correctamente');
    }

    public function updatePersonal(Request $request, Institucion $institucion, Asignacion $asignacion)
    {
        $this->perteneceA($institucion, $asignacion);

        $datos = $request->validate($this->reglasPersona($institucion, $asignacion->personal_id));

        DB::transaction(function () use ($datos, $institucion, $asignacion) {
            $this->liberarCoordinacion($asignacion);

            $asignacion->personal->update($this->datosPersona($datos));

            $programa = ProgramaEstudio::findOrFail($datos['programa_estudio_id']);

            $existente = Asignacion::where('personal_id', $asignacion->personal_id)
                ->where('programa_estudio_id', $programa->id)
                ->where('cargo', $datos['cargo'])
                ->where('id', '!=', $asignacion->id)
                ->first();

            if ($existente) {
                // Ya tenía ese mismo cargo en ese programa: se unifican
                $asignacion->delete();
                $asignacion = $existente;
            } else {
                $asignacion->update([
                    'programa_estudio_id' => $programa->id,
                    'cargo'               => $datos['cargo'],
                ]);
            }

            $this->fijarCoordinacion($asignacion);
        });

        return redirect()->back()->with('success', 'Personal actualizado correctamente');
    }

    public function destroyPersonal(Institucion $institucion, Asignacion $asignacion)
    {
        $this->perteneceA($institucion, $asignacion);

        DB::transaction(function () use ($asignacion) {
            $this->liberarCoordinacion($asignacion);
            $asignacion->delete();
        });

        return redirect()->back()->with('success', 'Se quitó al personal del programa correctamente');
    }

    /* ------------------------------- Auxiliares ------------------------------- */

    private function validar(Request $request, ?int $id = null): array
    {
        $datos = $request->validate([
            'ruc'       => ['required', 'digits:11', Rule::unique('instituciones', 'ruc')->ignore($id)],
            'nombre'    => ['required', 'string', 'max:150'],
            'tipo'      => ['required', Rule::in(Institucion::TIPOS)],
            'direccion' => ['nullable', 'string', 'max:150'],
        ]);

        $datos['nombre']    = mb_strtoupper(trim($datos['nombre']));
        $datos['direccion'] = isset($datos['direccion']) ? trim($datos['direccion']) : null;

        return $datos;
    }

    private function reglasPersona(Institucion $institucion, ?int $personalId = null): array
    {
        $dni = ['required', 'digits:8'];
        if ($personalId) {
            $dni[] = Rule::unique('personal', 'dni')->ignore($personalId);
        }

        return [
            'apellidos' => ['required', 'string', 'max:60'],
            'nombres'   => ['required', 'string', 'max:60'],
            'dni'       => $dni,
            'celular'   => ['nullable', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'cargo'     => ['required', Rule::in(Asignacion::CARGOS)],
            'programa_estudio_id' => [
                'required',
                Rule::exists('programas_estudio', 'id')
                    ->where('institucion_id', $institucion->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    private function datosPersona(array $datos): array
    {
        return [
            'apellidos' => mb_strtoupper(trim($datos['apellidos'])),
            'nombres'   => mb_strtoupper(trim($datos['nombres'])),
            'dni'       => $datos['dni'],
            'celular'   => $datos['celular'] ?? null,
            'email'     => $datos['email'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
        ];
    }

    private function perteneceA(Institucion $institucion, Asignacion $asignacion): void
    {
        abort_unless(
            $asignacion->programaEstudio
                && (int) $asignacion->programaEstudio->institucion_id === (int) $institucion->id,
            404
        );
    }

    private function fijarCoordinacion(Asignacion $asignacion): void
    {
        if ($asignacion->cargo === 'COORDINADOR') {
            ProgramaEstudio::where('id', $asignacion->programa_estudio_id)
                ->update(['coordinador_id' => $asignacion->personal_id]);
        }
    }

    private function liberarCoordinacion(Asignacion $asignacion): void
    {
        if ($asignacion->cargo === 'COORDINADOR') {
            ProgramaEstudio::where('id', $asignacion->programa_estudio_id)
                ->where('coordinador_id', $asignacion->personal_id)
                ->update(['coordinador_id' => null]);
        }
    }
}
