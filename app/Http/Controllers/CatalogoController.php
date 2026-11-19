<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\ProgramaEstudio;
use App\Models\Sala;
use App\Models\Tipo;
use App\Models\Usuario;
use App\Support\ContextoInstitucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CatalogoController extends Controller
{
    private const MODELOS = [
        'marca'  => Marca::class,
        'tipo'   => Tipo::class,
        'modelo' => Modelo::class,
        'sala'   => Sala::class,
        'grupo'  => Grupo::class,
    ];

    private const ESTADO_INACTIVO = 6;

    public function index($catalogo)
    {
        $this->autorizar($catalogo, 'ver');

        /** @var Usuario $user */$user           = Auth::user();
        $institucionId  = ContextoInstitucion::id();$programaId     = ContextoInstitucion::programaId();
        $idsPermitidos  =$user->idsProgramasPermitidos();
        $puedeCrear     =$this->puede($catalogo, 'crear');$puedeEditar    = $this->puede($catalogo, 'editar');
        $puedeEliminar  =$this->puede($catalogo, 'eliminar');$tablaProgramas = (new ProgramaEstudio())->getTable();

        $programasEstudio =$catalogo !== 'estado'
            ? DB::table("$tablaProgramas as pe")
                ->leftJoin('instituciones as inst', 'inst.id', '=', 'pe.institucion_id')
                ->whereIn('pe.id', $idsPermitidos)
                ->whereNull('pe.deleted_at')
                ->orderBy('pe.nombre')
                ->get(['pe.id', 'pe.nombre', 'inst.nombre as institucion_nombre'])
            : collect();

        $vista = compact('catalogo', 'puedeCrear', 'puedeEditar', 'puedeEliminar', 'programasEstudio');

        if ($catalogo === 'estado') {$estados = DB::select('CALL sp_listar_estados()');
            return view('almacen.catalogo.estado', $vista + compact('estados'));
        }

        $tablas = [
            'marca'  => ['tabla' => 'marcas',  'var' => 'marcas'],
            'tipo'   => ['tabla' => 'tipos',   'var' => 'tipos'],
            'modelo' => ['tabla' => 'modelos', 'var' => 'modelos'],
            'sala'   => ['tabla' => 'salas',   'var' => 'salas'],
            'grupo'  => ['tabla' => 'grupos',  'var' => 'grupos'],
        ];

        $tabla =$tablas[$catalogo]['tabla'];$var   = $tablas[$catalogo]['var'];

        $query = DB::table("$tabla as t")
            ->leftJoin("$tablaProgramas as pe", 'pe.id', '=', 't.programa_estudio_id')
            ->leftJoin('instituciones as inst', 'inst.id', '=', 'pe.institucion_id')
            ->whereNull('t.deleted_at')
            ->select(
                't.*',
                'pe.nombre as programa_estudio_nombre',
                'inst.nombre as institucion_nombre'
            )
            ->orderBy('t.nombre');

        if ($user->esSuperusuario()) {
            if ($institucionId !== null) {
                $query->where('pe.institucion_id',$institucionId);
            }
        } elseif ($user->tipo_usuario === 'ADMIN_INSTITUCION') {
            $query->whereIn('t.programa_estudio_id',$idsPermitidos);
        } elseif ($programaId !== null) {
            $query->where('t.programa_estudio_id',$programaId);
        }

        $registros =$query->get();

        return view("almacen.catalogo.$catalogo", $vista + [$var =>$registros]);
    }

    public function store(Request $request,$catalogo)
    {
        $this->autorizar($catalogo, 'crear');

        $userId = Auth::id();

        if ($catalogo === 'estado') {$request->validate(['nombre' => 'required|string|max:60']);
            DB::statement('CALL sp_guardar_estado(?, ?)', [$request->nombre,$userId]);

            return redirect()->back()->with('success', 'Estado registrado correctamente');
        }

        $programaDestinoId = $this->resolverProgramaDestino($request);
        if ($programaDestinoId === null) {
            return redirect()->back()->with('aviso_error', 'Debe seleccionar o tener un programa de estudio activo.');
        }

        $request->validate([
            'nombre'      => 'required|string|max:60',
            'observacion' => 'nullable|string|max:200',
        ]);

        if ($catalogo === 'grupo')  DB::statement('CALL sp_guardar_grupo(?, ?, ?)', [$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'marca')  DB::statement('CALL sp_guardar_marca(?, ?, ?)', [$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_guardar_tipo(?, ?, ?)', [$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_guardar_modelo(?, ?, ?)', [$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_guardar_sala(?, ?, ?, ?)', [$request->nombre,$request->observacion ?? null, $programaDestinoId,$userId]);

        return redirect()->back()->with('success', ucfirst($catalogo) . ' registrado correctamente');
    }

    public function update(Request $request, $catalogo,$id)
    {
        $this->autorizar($catalogo, 'editar');$this->verificarPertenencia($catalogo,$id);

        $userId = Auth::id();

        if ($catalogo === 'estado') {$request->validate(['nombre' => 'required|string|max:60']);
            DB::statement('CALL sp_actualizar_estado(?, ?, ?)', [$id, $request->nombre,$userId]);

            return redirect()->back()->with('success', 'Estado actualizado correctamente');
        }

        $clase    = self::MODELOS[$catalogo];$registro = $clase::findOrFail($id);

        $programaDestinoId =$this->resolverProgramaDestino($request, (int)$registro->programa_estudio_id);

        $request->validate([
            'nombre'      => 'required|string|max:60',
            'observacion' => 'nullable|string|max:200',
        ]);

        if ($catalogo === 'grupo')  DB::statement('CALL sp_actualizar_grupo(?, ?, ?, ?)', [$id,$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'marca')  DB::statement('CALL sp_actualizar_marca(?, ?, ?, ?)', [$id,$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_actualizar_tipo(?, ?, ?, ?)', [$id,$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_actualizar_modelo(?, ?, ?, ?)', [$id,$request->nombre, $programaDestinoId,$userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_actualizar_sala(?, ?, ?, ?, ?)', [$id, $request->nombre,$request->observacion ?? null, $programaDestinoId,$userId]);

        return redirect()->back()->with('success', ucfirst($catalogo) . ' actualizado correctamente');
    }

    public function destroy($catalogo,$id)
    {
        $this->autorizar($catalogo, 'eliminar');$this->verificarPertenencia($catalogo,$id);

        $userId = Auth::id();

        if ($catalogo === 'marca')  DB::statement('CALL sp_eliminar_marca(?, ?)', [$id,$userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_eliminar_tipo(?, ?)', [$id,$userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_eliminar_modelo(?, ?)', [$id,$userId]);
        if ($catalogo === 'estado') DB::statement('CALL sp_eliminar_estado(?, ?)', [$id,$userId]);
        if ($catalogo === 'grupo')  DB::statement('CALL sp_eliminar_grupo(?, ?)', [$id,$userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_eliminar_sala(?, ?)', [$id,$userId]);

        return redirect()->back()->with('success', ucfirst($catalogo) . ' eliminado correctamente');
    }

    private function resolverProgramaDestino(Request $request, ?int $programaActual = null): ?int
    {
        /** @var Usuario $user */$user = Auth::user();

        if ($user->esSuperusuario() || $user->tipo_usuario === 'ADMIN_INSTITUCION') {$request->validate([
                'programa_estudio_id' => ['required', 'integer', $this->reglaProgramaPermitido()],
            ]);

            return (int) $request->programa_estudio_id;
        }

        return $programaActual ?? ContextoInstitucion::programaId();
    }

    private function puede(string $catalogo, string$accion): bool
    {
        return Auth::user()->puede("catalogo.$catalogo.$accion");
    }

    private function autorizar(string $catalogo, string$accion): void
    {
        abort_unless($catalogo === 'estado' || isset(self::MODELOS[$catalogo]), 404);
        abort_unless($this->puede($catalogo,$accion), 403, 'Su rol no tiene permiso para realizar esta acción.');
    }

    private function verificarPertenencia(string $catalogo,$id): void
    {
        if ($catalogo === 'estado') {
            abort_if((int) $id === self::ESTADO_INACTIVO, 403, 'El estado INACTIVO es del sistema y no se puede modificar.');
            return;
        }

        $clase    = self::MODELOS[$catalogo];$registro = $clase::findOrFail($id);

        abort_unless(Auth::user()->puedeUsarPrograma((int) $registro->programa_estudio_id), 404);
    }

    private function reglaProgramaPermitido()
    {
        return Rule::in(Auth::user()->idsProgramasPermitidos());
    }
}