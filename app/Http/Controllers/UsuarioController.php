<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\ProgramaEstudio;
use App\Models\Usuario;
use App\Support\ContextoInstitucion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Usuarios de la institución activa. Los crea el administrador de la institución
 * (o el superusuario con una institución elegida) a partir del personal registrado.
 */
class UsuarioController extends Controller
{
    /** Roles que pueden tener los usuarios del personal. ADMIN_INSTITUCION solo lo asigna el superusuario. */
    private const ROLES_DEL_PERSONAL = [Usuario::COORDINADOR, Usuario::ASISTENTE, Usuario::DOCENTE];

    public function index()
    {
        $usuarios = $this->consulta()
            ->with(['personal', 'institucion', 'programas'])
            ->orderBy('username')
            ->get();

        // Personal de la institución (ya filtrado por el scope) que todavía no tiene usuario
        $conUsuario = Usuario::whereNotNull('personal_id')->pluck('personal_id');
        $personal   = Personal::with('asignaciones')
            ->whereNotIn('id', $conUsuario)
            ->orderBy('apellidos')->orderBy('nombres')
            ->get();

        $programas = ProgramaEstudio::where('activo', true)->orderBy('nombre')->get();
        $roles     = collect(Usuario::ROLES)->only($this->rolesAsignables());
        $sinInstitucion = ContextoInstitucion::id() === null;     // superusuario viendo "Todas"

        return view('usuarios.index', compact('usuarios', 'personal', 'programas', 'roles', 'sinInstitucion'));
    }

    public function store(Request $request)
    {
        $institucionId = ContextoInstitucion::id();

        if ($institucionId === null) {
            return redirect()->back()
                ->with('error', 'Elija primero una institución activa (menú de usuario) para crear usuarios.');
        }

        $datos = $request->validate([
            'personal_id'  => ['required', 'integer'],
            'username'     => array_merge(['nullable'], Usuario::reglasUsername()),
            'password'     => ['nullable', 'string', 'min:6', 'max:60'],
            'tipo_usuario' => ['required', Rule::in($this->rolesAsignables())],
            'programas'    => [$this->exigeProgramas($request) ? 'required' : 'nullable', 'array'],
            'programas.*'  => ['integer', ContextoInstitucion::reglaPrograma()],
        ], $this->mensajes());

        // Personal::find respeta el scope: solo encuentra personal de esta institución
        $persona = Personal::find($datos['personal_id']);

        if (! $persona) {
            throw ValidationException::withMessages(['personal_id' => 'La persona no pertenece a esta institución.']);
        }

        if (Usuario::where('personal_id', $persona->id)->exists()) {
            throw ValidationException::withMessages(['personal_id' => 'Esa persona ya tiene un usuario.']);
        }

        $username = $datos['username'] ?? $persona->dni;          // por defecto, su DNI
        $clave    = $datos['password'] ?? Str::password(10, symbols: false);

        if (Usuario::withTrashed()->where('username', $username)->exists()) {
            throw ValidationException::withMessages(['username' => 'Ese nombre de usuario ya está en uso.']);
        }

        DB::transaction(function () use ($datos, $persona, $username, $clave, $institucionId) {
            $usuario = Usuario::create([
                'username'       => $username,
                'password'       => $clave,
                'personal_id'    => $persona->id,
                'institucion_id' => $institucionId,              // siempre la institución activa
                'tipo_usuario'   => $datos['tipo_usuario'],
                'activo'         => true,
            ]);

            $this->sincronizarProgramas($usuario, $datos['programas'] ?? []);
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente')
            ->with('credenciales', ['usuario' => $username, 'clave' => $clave]);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $this->propio($usuario);

        $datos = $request->validate([
            'username'     => array_merge(['required'], Usuario::reglasUsername($usuario->id)),
            'password'     => ['nullable', 'string', 'min:6', 'max:60'],
            'tipo_usuario' => ['required', Rule::in($this->rolesAsignables())],
            'programas'    => [$this->exigeProgramas($request) ? 'required' : 'nullable', 'array'],
            'programas.*'  => ['integer', ContextoInstitucion::reglaPrograma()],
        ], $this->mensajes());

        $activo = $request->boolean('activo');

        if ($usuario->id === auth()->id() && ($datos['tipo_usuario'] !== $usuario->tipo_usuario || ! $activo)) {
            return redirect()->back()->with('error', 'No puede cambiar su propio rol ni desactivar su propia cuenta.');
        }

        DB::transaction(function () use ($usuario, $datos, $activo) {
            $cambios = [
                'username'     => $datos['username'],
                'tipo_usuario' => $datos['tipo_usuario'],
                'activo'       => $activo,
            ];

            if (! empty($datos['password'])) {
                $cambios['password'] = $datos['password'];
            }

            $usuario->update($cambios);
            $this->sincronizarProgramas($usuario, $datos['programas'] ?? []);
        });

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(Usuario $usuario)
    {
        $this->propio($usuario);

        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puede eliminar su propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }

    /* ------------------------------- Auxiliares ------------------------------- */

    /**
     * Usuarios que el usuario actual puede gestionar. Usuario no lleva el scope global
     * (se evita una recursión con el login), así que el filtro se aplica aquí.
     */
    private function consulta(): Builder
    {
        $q = Usuario::query()->whereNotNull('institucion_id');       // nunca los superusuarios

        if (($id = ContextoInstitucion::id()) !== null) {
            $q->where('institucion_id', $id);
        }

        return $q;
    }

    private function propio(Usuario $usuario): void
    {
        abort_unless($this->consulta()->whereKey($usuario->id)->exists(), 404);

        // Las cuentas de administrador de institución las maneja solo el superusuario
        // (Instituciones > Editar), no otro director.
        abort_if(
            $usuario->tipo_usuario === Usuario::ADMIN_INSTITUCION && ! auth()->user()->esSuperusuario(),
            403,
            'Solo el superusuario puede modificar cuentas de administrador de institución.'
        );
    }

    /** El director asigna coordinador, asistente y docente; el superusuario además administrador. */
    private function rolesAsignables(): array
    {
        return auth()->user()->esSuperusuario()
            ? array_merge([Usuario::ADMIN_INSTITUCION], self::ROLES_DEL_PERSONAL)
            : self::ROLES_DEL_PERSONAL;
    }

    /** Coordinador, asistente y docente necesitan al menos un programa; si no, entrarían y no verían nada. */
    private function exigeProgramas(Request $request): bool
    {
        return in_array($request->input('tipo_usuario'), self::ROLES_DEL_PERSONAL, true);
    }

    private function mensajes(): array
    {
        return [
            'programas.required' => 'Seleccione al menos un programa de estudio para este rol.',
        ];
    }

    private function sincronizarProgramas(Usuario $usuario, array $ids): void
    {
        $usuario->programas()->sync(
            collect($ids)->mapWithKeys(fn ($id) => [(int) $id => ['acceso' => true]])->all()
        );
    }
}
