<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\ProgramaEstudio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstitucionController extends Controller
{
    public function index()
    {
        $instituciones = Institucion::withCount('programas')
            ->with(['usuarios' => fn ($q) => $q->where('tipo_usuario', Usuario::ADMIN_INSTITUCION)->orderBy('id')])
            ->orderBy('nombre')
            ->get();

        return view('organizacion.instituciones', compact('instituciones'));
    }

    public function show(Institucion $institucion)
    {
        /** @var Usuario $user */
        $user           = Auth::user();
        $puedeGestionar = $user->puede('institucion.gestionar');

        $programas = $institucion->programas()
            ->with('coordinador')
            ->orderBy('nombre');

        if (! $user->veTodaLaInstitucion()) {
            $programas->whereIn('id', $user->idsProgramasPermitidos());
        }

        $programas = $programas->get();

        return view('organizacion.institucion', compact('institucion', 'programas', 'puedeGestionar'));
    }

    public function store(Request $request)
    {
        $datos  = $this->validar($request);
        $acceso = $request->validate([
            'username' => array_merge(['nullable'], Usuario::reglasUsername()),
            'password' => ['nullable', 'string', 'min:6', 'max:60'],
        ]);

        $username = $acceso['username'] ?? $datos['ruc'];
        $clave    = $acceso['password'] ?? Str::password(10, symbols: false);

        if (Usuario::withTrashed()->where('username', $username)->exists()) {
            return redirect()->back()->withInput()
                ->withErrors(['username' => 'Ese nombre de usuario ya está en uso.']);
        }

        DB::transaction(function () use ($datos, $username, $clave) {
            $institucion = Institucion::create($datos);

            Usuario::create([
                'username'       => $username,
                'password'       => $clave,
                'institucion_id' => $institucion->id,
                'tipo_usuario'   => Usuario::ADMIN_INSTITUCION,
                'activo'         => true,
            ]);
        });

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución registrada y usuario de acceso creado correctamente')
            ->with('credenciales', ['usuario' => $username, 'clave' => $clave]);
    }

    public function update(Request $request, Institucion $institucion)
    {
        $datos = $this->validar($request, $institucion->id);

        $usuario = $institucion->usuarios()
            ->where('tipo_usuario', Usuario::ADMIN_INSTITUCION)
            ->orderBy('id')
            ->first();

        $acceso = $request->validate([
            'username' => array_merge(['nullable'], Usuario::reglasUsername($usuario?->id)),
            'password' => ['nullable', 'string', 'min:6', 'max:60'],
        ]);

        $credenciales = null;

        DB::transaction(function () use ($institucion, $datos, $usuario, $acceso, &$credenciales) {
            $institucion->update($datos);

            if ($usuario) {
                $cambios = [];

                if (! empty($acceso['username'])) {
                    $cambios['username'] = $acceso['username'];
                }
                if (! empty($acceso['password'])) {
                    $cambios['password'] = $acceso['password'];
                }
                if ($cambios) {
                    $usuario->update($cambios);
                }

                return;
            }

            $username = $acceso['username'] ?? $datos['ruc'];
            $clave    = $acceso['password'] ?? Str::password(10, symbols: false);

            if (Usuario::withTrashed()->where('username', $username)->exists()) {
                throw ValidationException::withMessages(['username' => 'Ese nombre de usuario ya está en uso.']);
            }

            Usuario::create([
                'username'       => $username,
                'password'       => $clave,
                'institucion_id' => $institucion->id,
                'tipo_usuario'   => Usuario::ADMIN_INSTITUCION,
                'activo'         => true,
            ]);

            $credenciales = ['usuario' => $username, 'clave' => $clave];
        });

        $respuesta = redirect()->back()->with('success', 'Institución actualizada correctamente');

        return $credenciales ? $respuesta->with('credenciales', $credenciales) : $respuesta;
    }

    public function destroy(Institucion $institucion)
    {
        if ($institucion->programas()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar: la institución tiene programas de estudio asociados.');
        }

        DB::transaction(function () use ($institucion) {
            $institucion->usuarios()->delete();
            $institucion->delete();
        });

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente');
    }

    public function storePrograma(Request $request, Institucion $institucion)
    {
        $datos = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'abreviatura' => ['nullable', 'string', 'max:30'],
            'observacion' => ['nullable', 'string', 'max:150'],
        ]);

        $nombre = mb_strtoupper(trim($datos['nombre']));

        if ($institucion->programas()->where('nombre', $nombre)->exists()) {
            return redirect()->back()->with('error', 'Ese programa de estudio ya está registrado en la institución.');
        }

        $institucion->programas()->create([
            'nombre'      => $nombre,
            'abreviatura' => ! empty($datos['abreviatura']) ? mb_strtoupper(trim($datos['abreviatura'])) : null,
            'observacion' => ! empty($datos['observacion']) ? trim($datos['observacion']) : null,
            'activo'      => true,
        ]);

        return redirect()->back()->with('success', 'Programa de estudio registrado correctamente');
    }

    public function updatePrograma(Request $request, Institucion $institucion, ProgramaEstudio $programa)
    {
        abort_unless((int) $programa->institucion_id === (int) $institucion->id, 404);

        $datos = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'abreviatura' => ['nullable', 'string', 'max:30'],
            'observacion' => ['nullable', 'string', 'max:150'],
        ]);

        $nombre = mb_strtoupper(trim($datos['nombre']));

        if ($institucion->programas()->where('nombre', $nombre)->where('id', '!=', $programa->id)->exists()) {
            return redirect()->back()->with('error', 'Ya existe otro programa de estudio con ese nombre en la institución.');
        }

        $programa->update([
            'nombre'      => $nombre,
            'abreviatura' => ! empty($datos['abreviatura']) ? mb_strtoupper(trim($datos['abreviatura'])) : null,
            'observacion' => ! empty($datos['observacion']) ? trim($datos['observacion']) : null,
            'activo'      => $request->boolean('activo', true),
        ]);

        return redirect()->back()->with('success', 'Programa de estudio actualizado correctamente');
    }

    public function destroyPrograma(Institucion $institucion, ProgramaEstudio $programa)
    {
        abort_unless((int) $programa->institucion_id === (int) $institucion->id, 404);

        if ($programa->salas()->exists() || $programa->inventarios()->exists() || $programa->asignaciones()->exists() || $programa->usuarios()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar: el programa tiene personal, salas, inventarios o usuarios asociados.');
        }

        $programa->delete();

        return redirect()->back()->with('success', 'Programa de estudio eliminado correctamente');
    }

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
}