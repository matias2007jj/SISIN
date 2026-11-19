<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\ProgramaEstudio;
use App\Models\Sala;
use App\Models\Tipo;
use App\Support\ContextoInstitucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CatalogoController extends Controller
{
    /** Catálogos que pertenecen a un programa de estudio. */
    private const MODELOS = [
        'marca'  => Marca::class,
        'tipo'   => Tipo::class,
        'modelo' => Modelo::class,
        'sala'   => Sala::class,
        'grupo'  => Grupo::class,
    ];

    /** Estado INACTIVO: el sistema depende de él (baja de bienes), no se edita ni se borra. */
    private const ESTADO_INACTIVO = 6;

    public function index($catalogo)
    {
        $this->autorizar($catalogo, 'ver');

        $programaId   = ContextoInstitucion::programaId();
        $puedeCrear   = $this->puede($catalogo, 'crear');
        $puedeEditar  = $this->puede($catalogo, 'editar');
        $puedeEliminar = $this->puede($catalogo, 'eliminar');
        $vista = compact('catalogo', 'puedeCrear', 'puedeEditar', 'puedeEliminar');

        if ($catalogo === 'marca') {
            $marcas = DB::select('CALL sp_listar_marcas(?)', [$programaId]);
            return view('almacen.catalogo.marca', $vista + compact('marcas'));
        }

        if ($catalogo === 'tipo') {
            $tipos = DB::select('CALL sp_listar_tipos(?)', [$programaId]);
            return view('almacen.catalogo.tipo', $vista + compact('tipos'));
        }

        if ($catalogo === 'modelo') {
            $modelos = DB::select('CALL sp_listar_modelos(?)', [$programaId]);
            return view('almacen.catalogo.modelo', $vista + compact('modelos'));
        }

        if ($catalogo === 'estado') {
            $estados = DB::select('CALL sp_listar_estados()');
            return view('almacen.catalogo.estado', $vista + compact('estados'));
        }

        if ($catalogo === 'grupo') {
            $grupos = DB::select('CALL sp_listar_grupos(?, 0)', [$programaId]);

            // Solo los programas a los que el usuario tiene acceso
            $programasEstudio = ProgramaEstudio::whereIn('id', Auth::user()->idsProgramasPermitidos())
                ->orderBy('nombre')
                ->get(['id', 'nombre']);

            return view('almacen.catalogo.grupo', $vista + compact('grupos', 'programasEstudio'));
        }

        // sala
        $salas = DB::select('CALL sp_listar_salas(?)', [$programaId]);
        return view('almacen.catalogo.sala', $vista + compact('salas'));
    }

    public function store(Request $request, $catalogo)
    {
        $this->autorizar($catalogo, 'crear');

        $userId     = Auth::id();
        $programaId = ContextoInstitucion::programaId();

        if ($catalogo === 'estado') {
            $request->validate(['nombre' => 'required|string|max:60']);
            DB::statement('CALL sp_guardar_estado(?, ?)', [$request->nombre, $userId]);

            return redirect()->back()->with('success', 'Estado registrado correctamente');
        }

        if ($catalogo === 'grupo') {
            $request->validate([
                'nombre'              => 'required|string|max:60',
                'programa_estudio_id' => ['required', 'integer', $this->reglaProgramaPermitido()],
            ]);

            DB::statement('CALL sp_guardar_grupo(?, ?, ?)', [$request->nombre, $request->programa_estudio_id, $userId]);

            return redirect()->back()->with('success', 'Grupo registrado correctamente');
        }

        // marca, tipo, modelo, sala: se guardan en el programa activo
        if ($programaId === null) {
            return redirect()->back()->with('aviso_error', 'No tiene un programa de estudio activo.');
        }

        $request->validate(['nombre' => 'required|string|max:60']);

        if ($catalogo === 'marca')  DB::statement('CALL sp_guardar_marca(?, ?, ?)', [$request->nombre, $programaId, $userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_guardar_tipo(?, ?, ?)', [$request->nombre, $programaId, $userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_guardar_modelo(?, ?, ?)', [$request->nombre, $programaId, $userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_guardar_sala(?, ?, ?, ?)', [$request->nombre, $request->descripcion ?? null, $programaId, $userId]);

        return redirect()->back()->with('success', ucfirst($catalogo) . ' registrado correctamente');
    }

    public function update(Request $request, $catalogo, $id)
    {
        $this->autorizar($catalogo, 'editar');
        $this->verificarPertenencia($catalogo, $id);

        $userId = Auth::id();

        if ($catalogo === 'grupo') {
            $request->validate([
                'nombre'              => 'required|string|max:60',
                'programa_estudio_id' => ['required', 'integer', $this->reglaProgramaPermitido()],
            ]);

            DB::statement('CALL sp_actualizar_grupo(?, ?, ?, ?)', [$id, $request->nombre, $request->programa_estudio_id, $userId]);

            return redirect()->back()->with('success', 'Grupo actualizado correctamente');
        }

        $request->validate(['nombre' => 'required|string|max:60']);

        if ($catalogo === 'marca')  DB::statement('CALL sp_actualizar_marca(?, ?, ?)', [$id, $request->nombre, $userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_actualizar_tipo(?, ?, ?)', [$id, $request->nombre, $userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_actualizar_modelo(?, ?, ?)', [$id, $request->nombre, $userId]);
        if ($catalogo === 'estado') DB::statement('CALL sp_actualizar_estado(?, ?, ?)', [$id, $request->nombre, $userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_actualizar_sala(?, ?, ?, ?)', [$id, $request->nombre, $request->descripcion ?? null, $userId]);

        return redirect()->back()->with('success', ucfirst($catalogo) . ' actualizado correctamente');
    }

    public function destroy($catalogo, $id)
    {
        $this->autorizar($catalogo, 'eliminar');
        $this->verificarPertenencia($catalogo, $id);

        $userId = Auth::id();

        if ($catalogo === 'marca')  DB::statement('CALL sp_eliminar_marca(?, ?)', [$id, $userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_eliminar_tipo(?, ?)', [$id, $userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_eliminar_modelo(?, ?)', [$id, $userId]);
        if ($catalogo === 'estado') DB::statement('CALL sp_eliminar_estado(?, ?)', [$id, $userId]);
        if ($catalogo === 'grupo')  DB::statement('CALL sp_eliminar_grupo(?, ?)', [$id, $userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_eliminar_sala(?, ?)', [$id, $userId]);

        return redirect()->back()->with('success', ucfirst($catalogo) . ' eliminado correctamente');
    }

    /* ------------------------------- Auxiliares ------------------------------- */

    private function puede(string $catalogo, string $accion): bool
    {
        return Auth::user()->puede("catalogo.$catalogo.$accion");
    }

    /** El catálogo debe existir y el rol debe tener permiso para esa acción. */
    private function autorizar(string $catalogo, string $accion): void
    {
        abort_unless($catalogo === 'estado' || isset(self::MODELOS[$catalogo]), 404);
        abort_unless($this->puede($catalogo, $accion), 403, 'Su rol no tiene permiso para realizar esta acción.');
    }

    /**
     * El registro a editar o borrar debe ser de un programa al que el usuario tiene acceso
     * (los procedimientos almacenados solo reciben el id y no lo comprueban).
     */
    private function verificarPertenencia(string $catalogo, $id): void
    {
        if ($catalogo === 'estado') {
            abort_if((int) $id === self::ESTADO_INACTIVO, 403, 'El estado INACTIVO es del sistema y no se puede modificar.');
            return;
        }

        $clase    = self::MODELOS[$catalogo];
        $registro = $clase::findOrFail($id);

        abort_unless(Auth::user()->puedeUsarPrograma((int) $registro->programa_estudio_id), 404);
    }

    private function reglaProgramaPermitido()
    {
        return Rule::in(Auth::user()->idsProgramasPermitidos());
    }
}
