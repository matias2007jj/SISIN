<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Personal;
use App\Models\ProgramaEstudio;
use App\Models\Scopes\InstitucionScope;
use App\Models\Usuario;
use App\Support\ContextoInstitucion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    private const ROLES_DEL_PERSONAL = [Usuario::COORDINADOR, Usuario::ASISTENTE, Usuario::DOCENTE];

    public function index()
    {
        $usuarios = $this->consulta()
            ->with(['personal', 'institucion', 'programas'])
            ->orderBy('username')
            ->get();

        $programas      = ProgramaEstudio::where('activo', true)->orderBy('nombre')->get();
        $roles          = collect(Usuario::ROLES)->only($this->rolesAsignables());
        $sinInstitucion = ContextoInstitucion::id() === null;

        return view('usuarios.index', compact('usuarios', 'programas', 'roles', 'sinInstitucion'));
    }

    public function store(Request $request)
    {
        $institucionId = ContextoInstitucion::id();

        if ($institucionId === null) {
            return redirect()->back()
                ->with('error', 'Elija primero una institución activa (menú de usuario) para crear usuarios.');
        }

        $esPersonal = $this->exigeDatosPersonal($request);

        $personaExistente = $esPersonal && $request->filled('dni')
            ? Personal::withoutGlobalScope(InstitucionScope::class)->withTrashed()->where('dni', $request->input('dni'))->first()
            : null;

        if ($personaExistente && Usuario::where('personal_id', $personaExistente->id)->exists()) {
            throw ValidationException::withMessages(['dni' => 'Ya existe un usuario registrado con ese DNI.']);
        }

        $datos = $request->validate(
            $this->reglas($request, null, $personaExistente?->id),
            $this->mensajes()
        );

        $username = ! empty($datos['username'])
            ? $datos['username']
            : ($datos['dni'] ?? Str::lower(Str::random(8)));

        $clave = ! empty($datos['password'])
            ? $datos['password']
            : Str::password(10, symbols: false);

        if (Usuario::withTrashed()->where('username', $username)->exists()) {
            throw ValidationException::withMessages(['username' => 'Ese nombre de usuario ya está en uso.']);
        }

        DB::transaction(function () use ($datos, $esPersonal, $personaExistente, $username, $clave, $institucionId) {
            $personalId = null;

            if ($esPersonal) {
                $camposPersona = $this->datosPersona($datos);

                if ($personaExistente) {
                    if ($personaExistente->trashed()) {
                        $personaExistente->restore();
                    }
                    $personaExistente->update($camposPersona);
                    $persona = $personaExistente;
                } else {
                    $persona = Personal::create($camposPersona);
                }

                $personalId = $persona->id;
            }

            $usuario = Usuario::create([
                'username'       => $username,
                'password'       => $clave,
                'personal_id'    => $personalId,
                'institucion_id' => $institucionId,
                'tipo_usuario'   => $datos['tipo_usuario'],
                'activo'         => true,
            ]);

            $programaIds = $datos['programas'] ?? [];
            $this->sincronizarProgramas($usuario, $programaIds);

            if ($personalId) {
                $this->sincronizarAsignaciones($personalId, $institucionId, $datos['tipo_usuario'], $programaIds);
            }
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Personal y usuario registrados correctamente')
            ->with('credenciales', ['usuario' => $username, 'clave' => $clave]);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $this->propio($usuario);

        $esPersonal = $this->exigeDatosPersonal($request);

        $datos = $request->validate(
            $this->reglas($request, $usuario->id, $usuario->personal_id),
            $this->mensajes()
        );

        $activo = $request->boolean('activo');

        if ($usuario->id === auth()->id() && ($datos['tipo_usuario'] !== $usuario->tipo_usuario || ! $activo)) {
            return redirect()->back()->with('error', 'No puede cambiar su propio rol ni desactivar su propia cuenta.');
        }

        $credenciales = null;

        DB::transaction(function () use ($usuario, $datos, $esPersonal, $activo, &$credenciales) {
            $personalId = $usuario->personal_id;

            if ($esPersonal) {
                $camposPersona = $this->datosPersona($datos);

                if ($usuario->personal) {
                    $usuario->personal->update($camposPersona);
                } else {
                    $persona = Personal::withoutGlobalScope(InstitucionScope::class)
                        ->withTrashed()
                        ->where('dni', $datos['dni'])
                        ->first();

                    if ($persona) {
                        if ($persona->trashed()) {
                            $persona->restore();
                        }
                        $persona->update($camposPersona);
                    } else {
                        $persona = Personal::create($camposPersona);
                    }

                    $personalId = $persona->id;
                }
            }

            $cambios = [
                'username'     => $datos['username'],
                'personal_id'  => $personalId,
                'tipo_usuario' => $datos['tipo_usuario'],
                'activo'       => $activo,
            ];

            if (! empty($datos['password'])) {
                $cambios['password'] = $datos['password'];
                $credenciales        = ['usuario' => $datos['username'], 'clave' => $datos['password']];
            }

            $usuario->update($cambios);

            $programaIds = $datos['programas'] ?? [];
            $this->sincronizarProgramas($usuario, $programaIds);

            if ($personalId && $usuario->institucion_id) {
                $this->sincronizarAsignaciones(
                    $personalId,
                    (int) $usuario->institucion_id,
                    $datos['tipo_usuario'],
                    $programaIds
                );
            }
        });

        $respuesta = redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');

        return $credenciales ? $respuesta->with('credenciales', $credenciales) : $respuesta;
    }

    public function destroy(Usuario $usuario)
    {
        $this->propio($usuario);

        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puede eliminar su propia cuenta.');
        }

        DB::transaction(function () use ($usuario) {
            $personalId    = $usuario->personal_id;
            $institucionId = $usuario->institucion_id;

            $usuario->programas()->detach();
            $usuario->delete();

            if ($personalId) {
                if ($institucionId) {
                    $programasInst = ProgramaEstudio::withoutGlobalScope(InstitucionScope::class)
                        ->where('institucion_id', $institucionId)
                        ->pluck('id');

                    ProgramaEstudio::withoutGlobalScope(InstitucionScope::class)
                        ->whereIn('id', $programasInst)
                        ->where('coordinador_id', $personalId)
                        ->update(['coordinador_id' => null]);

                    Asignacion::where('personal_id', $personalId)
                        ->whereIn('programa_estudio_id', $programasInst)
                        ->delete();
                }

                $tieneOtrosUsuarios = Usuario::where('personal_id', $personalId)->exists();
                $tieneOtrasAsig     = Asignacion::where('personal_id', $personalId)->exists();

                if (! $tieneOtrosUsuarios && ! $tieneOtrasAsig) {
                    Personal::withoutGlobalScope(InstitucionScope::class)
                        ->where('id', $personalId)
                        ->delete();
                }
            }
        });

        return redirect()->route('usuarios.index')->with('success', 'Personal y usuario eliminados correctamente');
    }

    private function consulta(): Builder
    {
        $q = Usuario::query()->whereNotNull('institucion_id');

        if (($id = ContextoInstitucion::id()) !== null) {
            $q->where('institucion_id', $id);
        }

        return $q;
    }

    private function propio(Usuario $usuario): void
    {
        abort_unless($this->consulta()->whereKey($usuario->id)->exists(), 404);

        abort_if(
            $usuario->tipo_usuario === Usuario::ADMIN_INSTITUCION && ! auth()->user()->esSuperusuario(),
            403,
            'Solo el superusuario puede modificar cuentas de administrador de institución.'
        );
    }

    private function rolesAsignables(): array
    {
        return auth()->user()->esSuperusuario()
            ? array_merge([Usuario::ADMIN_INSTITUCION], self::ROLES_DEL_PERSONAL)
            : self::ROLES_DEL_PERSONAL;
    }

    private function exigeDatosPersonal(Request $request): bool
    {
        return in_array($request->input('tipo_usuario'), self::ROLES_DEL_PERSONAL, true);
    }

    private function reglas(Request $request, ?int $usuarioId = null, ?int $personalId = null): array
    {
        $esPersonal = $this->exigeDatosPersonal($request);
        $reqPersona = $esPersonal ? 'required' : 'nullable';

        $reglaDni = [$reqPersona, 'digits:8'];
        if ($personalId) {
            $reglaDni[] = Rule::unique('personal', 'dni')->ignore($personalId);
        }

        return [
            'apellidos'    => [$reqPersona, 'string', 'max:60', 'regex:/^[\pL\s\'-]+$/u'],
            'nombres'      => [$reqPersona, 'string', 'max:60', 'regex:/^[\pL\s\'-]+$/u'],
            'dni'          => $reglaDni,
            'celular'      => ['nullable', 'digits:9'],
            'email'        => ['nullable', 'email', 'max:100'],
            'direccion'    => ['nullable', 'string', 'max:150'],
            'username'     => array_merge([$usuarioId ? 'required' : 'nullable'], Usuario::reglasUsername($usuarioId)),
            'password'     => ['nullable', 'string', 'min:6', 'max:60'],
            'tipo_usuario' => ['required', Rule::in($this->rolesAsignables())],
            'programas'    => [$esPersonal ? 'required' : 'nullable', 'array'],
            'programas.*'  => ['integer', ContextoInstitucion::reglaPrograma()],
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

    private function mensajes(): array
    {
        return [
            'apellidos.regex'    => 'Los apellidos solo deben contener letras (sin números).',
            'nombres.regex'      => 'Los nombres solo deben contener letras (sin números).',
            'dni.digits'         => 'El DNI debe tener exactamente 8 dígitos numéricos.',
            'celular.digits'     => 'El número de celular debe tener exactamente 9 dígitos numéricos.',
            'programas.required' => 'Seleccione al menos un programa de estudio para este rol.',
        ];
    }

    private function sincronizarProgramas(Usuario $usuario, array $ids): void
    {
        $usuario->programas()->sync(
            collect($ids)->mapWithKeys(fn ($id) => [(int) $id => ['acceso' => true]])->all()
        );
    }

    private function sincronizarAsignaciones(int $personalId, int $institucionId, string $rol, array $programaIds): void
    {
        $ids = collect($programaIds)->map(fn ($id) => (int) $id)->unique()->values()->all();

        $programasInst = ProgramaEstudio::withoutGlobalScope(InstitucionScope::class)
            ->where('institucion_id', $institucionId)
            ->pluck('id');

        Asignacion::where('personal_id', $personalId)
            ->whereIn('programa_estudio_id', $programasInst)
            ->delete();

        ProgramaEstudio::withoutGlobalScope(InstitucionScope::class)
            ->whereIn('id', $programasInst)
            ->where('coordinador_id', $personalId)
            ->whereNotIn('id', $rol === Usuario::COORDINADOR ? $ids : [])
            ->update(['coordinador_id' => null]);

        if (! in_array($rol, self::ROLES_DEL_PERSONAL, true)) {
            return;
        }

        foreach ($ids as $progId) {
            Asignacion::create([
                'personal_id'         => $personalId,
                'programa_estudio_id' => $progId,
                'cargo'               => $rol,
            ]);
        }

        if ($rol === Usuario::COORDINADOR && $ids) {
            ProgramaEstudio::withoutGlobalScope(InstitucionScope::class)
                ->whereIn('id', $ids)
                ->update(['coordinador_id' => $personalId]);
        }
    }
}