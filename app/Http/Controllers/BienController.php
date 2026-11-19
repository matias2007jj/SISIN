<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Usuario;
use App\Support\ContextoInstitucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BienController extends Controller
{
    /** Estado "INACTIVO": solo se asigna con el botón de baja. */
    private const ESTADO_INACTIVO = 6;

    public function index()
    {
        /** @var Usuario $user */
        $user          = Auth::user();
        $institucionId = $user->institucionActiva()?->id;
        $programaId    = ContextoInstitucion::programaId();

        // Superusuario y director ven toda la institución; coordinador, asistente y docente, solo su programa activo
        $verInstitucion = $user->veTodaLaInstitucion();

        $bienes = DB::select('CALL sp_listar_bienes(?, ?, ?)', [
            $programaId,
            $institucionId,
            $verInstitucion ? 1 : 0,
        ]);

        // Combos aislados por el programa de estudio activo
        $salas   = $programaId ? DB::select('CALL sp_listar_salas(?)', [$programaId]) : [];
        $grupos  = $programaId ? DB::select('CALL sp_listar_grupos(?, 0)', [$programaId]) : [];
        $tipos   = $programaId ? DB::select('CALL sp_listar_tipos(?)', [$programaId]) : [];
        $marcas  = $programaId ? DB::select('CALL sp_listar_marcas(?)', [$programaId]) : [];
        $modelos = $programaId ? DB::select('CALL sp_listar_modelos(?)', [$programaId]) : [];

        // Excluye INACTIVO para que solo se asigne con el botón de baja
        $estados = collect(DB::select('CALL sp_listar_estados()'))
            ->where('id', '!=', self::ESTADO_INACTIVO);

        $puedeRegistrar = $user->puede('bienes.registrar') && $programaId !== null;
        $puedeInactivar = $user->puede('bienes.inactivar');

        return view('almacen.bien.index', compact(
            'bienes', 'salas', 'grupos', 'tipos', 'marcas', 'modelos', 'estados',
            'programaId', 'verInstitucion', 'puedeRegistrar', 'puedeInactivar'
        ));
    }

    public function store(Request $request)
    {
        $programaId = ContextoInstitucion::programaId();

        if ($programaId === null) {
            return redirect()->route('bienes.index')
                ->with('aviso_error', 'No tiene un programa de estudio activo para registrar bienes.');
        }

        $data = $request->validate($this->reglas($programaId), [
            'cbi.unique' => 'El código CBI ya se encuentra registrado en otro bien.',
        ]);

        DB::statement('CALL sp_guardar_bien(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['sala_id'],
            $data['grupo_id'],
            $data['cbi'],
            $data['tipo_id'],
            $data['descripcion'],
            $data['marca_id'] ?? null,
            $data['modelo_id'] ?? null,
            $data['serie_medida'] ?? null,
            $data['estado_id'],
            $data['otras_especificaciones'] ?? null,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'Bien registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $programaId = ContextoInstitucion::programaId();
        $bien       = $this->delProgramaActivo((int) $id, $programaId);

        // Quien no puede dar de baja tampoco puede reactivar lo que otro dio de baja
        if ((int) $bien->estado_id === self::ESTADO_INACTIVO && ! Auth::user()->puede('bienes.inactivar')) {
            abort(403, 'Solo el coordinador o el director pueden reactivar un bien inactivo.');
        }

        $data = $request->validate($this->reglas($programaId, $bien->id), [
            'cbi.unique' => 'El código CBI ya pertenece a otro bien.',
        ]);

        DB::statement('CALL sp_actualizar_bien(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $bien->id,
            $data['sala_id'],
            $data['grupo_id'],
            $data['cbi'],
            $data['tipo_id'],
            $data['descripcion'],
            $data['marca_id'] ?? null,
            $data['modelo_id'] ?? null,
            $data['serie_medida'] ?? null,
            $data['estado_id'],
            $data['otras_especificaciones'] ?? null,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'Bien actualizado correctamente.');
    }

    public function destroy($id)
    {
        $bien = $this->delProgramaActivo((int) $id, ContextoInstitucion::programaId());

        // Pasa el bien a estado INACTIVO sin borrar el registro
        DB::statement('CALL sp_desactivar_bien(?, ?)', [
            $bien->id,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'El bien pasó a estado INACTIVO.');
    }

    /* ------------------------------- Auxiliares ------------------------------- */

    /**
     * Reglas del formulario. Sala, grupo, tipo, marca y modelo deben ser del programa activo:
     * así nadie puede mandar el id de un dato de otro programa o de otra institución.
     */
    private function reglas(int $programaId, ?int $ignorarId = null): array
    {
        $delPrograma = fn (string $tabla) => Rule::exists($tabla, 'id')
            ->where('programa_estudio_id', $programaId)
            ->whereNull('deleted_at');

        return [
            'sala_id'                => ['required', 'integer', $delPrograma('salas')],
            'grupo_id'               => ['required', 'integer', $delPrograma('grupos')],
            'cbi'                    => ['required', 'string', 'max:10', Rule::unique('bienes', 'cbi')->ignore($ignorarId)->whereNull('deleted_at')],
            'tipo_id'                => ['required', 'integer', $delPrograma('tipos')],
            'descripcion'            => ['required', 'string', 'max:100'],
            'marca_id'               => ['nullable', 'integer', $delPrograma('marcas')],
            'modelo_id'              => ['nullable', 'integer', $delPrograma('modelos')],
            'serie_medida'           => ['nullable', 'string', 'max:100'],
            'estado_id'              => ['required', 'integer', Rule::exists('estados', 'id')->whereNull('deleted_at'), Rule::notIn([self::ESTADO_INACTIVO])],
            'otras_especificaciones' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** El bien debe existir y pertenecer al programa con el que el usuario está trabajando. */
    private function delProgramaActivo(int $id, ?int $programaId): Bien
    {
        abort_if($programaId === null, 403, 'No tiene un programa de estudio activo.');

        $bien = Bien::with('sala')->findOrFail($id);

        abort_unless((int) $bien->sala?->programa_estudio_id === $programaId, 404);

        return $bien;
    }
}
