<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\ProgramaEstudio;
use App\Models\Sala;
use App\Models\Tipo;
use App\Models\Usuario;
use App\Services\CatalogoExcelService;
use App\Support\ContextoInstitucion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

    private const TABLAS = [
        'marca'  => 'marcas',
        'tipo'   => 'tipos',
        'modelo' => 'modelos',
        'sala'   => 'salas',
        'grupo'  => 'grupos',
    ];

    private const TITULOS_PDF = [
        'grupo'  => 'CATÁLOGO DE GRUPOS',
        'sala'   => 'CATÁLOGO DE SALAS',
        'tipo'   => 'CATÁLOGO DE TIPOS',
        'marca'  => 'CATÁLOGO DE MARCAS',
        'modelo' => 'CATÁLOGO DE MODELOS',
        'estado' => 'CATÁLOGO DE ESTADOS',
    ];

    private const ESTADO_INACTIVO = 6;

    public function __construct(
        private readonly CatalogoExcelService $excelService
    ) {}

    public function index($catalogo)
    {
        $this->autorizar($catalogo, 'ver');

        /** @var Usuario $user */
        $user           = Auth::user();
        $institucionId  = ContextoInstitucion::id();
        $idsPermitidos  = $user->idsProgramasPermitidos();
        $puedeCrear     = $this->puede($catalogo, 'crear');
        $puedeEditar    = $this->puede($catalogo, 'editar');
        $puedeEliminar  = $this->puede($catalogo, 'eliminar');
        $tablaProgramas = (new ProgramaEstudio())->getTable();

        $programasEstudio = $catalogo !== 'estado'
            ? DB::table("$tablaProgramas as pe")
                ->leftJoin('instituciones as inst', 'inst.id', '=', 'pe.institucion_id')
                ->whereIn('pe.id', $idsPermitidos)
                ->whereNull('pe.deleted_at')
                ->when($user->esSuperusuario() && $institucionId !== null, function ($q) use ($institucionId) {
                    $q->where('pe.institucion_id', $institucionId);
                })
                ->orderBy('pe.nombre')
                ->get(['pe.id', 'pe.nombre', 'pe.institucion_id', 'inst.nombre as institucion_nombre'])
            : collect();

        $instituciones = ($catalogo !== 'estado' && $user->esSuperusuario())
            ? DB::table('instituciones')
                ->whereNull('deleted_at')
                ->when($institucionId !== null, fn ($q) => $q->where('id', $institucionId))
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
            : collect();

        $vista = compact('catalogo', 'puedeCrear', 'puedeEditar', 'puedeEliminar', 'programasEstudio', 'instituciones');

        if ($catalogo === 'estado') {
            $estados = DB::select('CALL sp_listar_estados()');
            return view('almacen.catalogo.estado', $vista + compact('estados'));
        }

        $vars = [
            'marca'  => 'marcas',
            'tipo'   => 'tipos',
            'modelo' => 'modelos',
            'sala'   => 'salas',
            'grupo'  => 'grupos',
        ];

        $registros = $this->obtenerRegistrosFiltrados($catalogo);

        return view("almacen.catalogo.$catalogo", $vista + [$vars[$catalogo] => $registros]);
    }

    public function importarExcel(Request $request, $catalogo)
    {
        $this->autorizar($catalogo, 'crear');

        $request->validate([
            'archivo_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'archivo_excel.required' => 'Seleccione un archivo Excel (.xlsx, .xls o .csv).',
            'archivo_excel.mimes'    => 'El archivo debe tener formato .xlsx, .xls o .csv.',
        ]);

        $resultado = $this->excelService->importar($catalogo, $request->file('archivo_excel'));

        $mensaje = "Importación completada: {$resultado['creados']} registro(s) creado(s)";
        if ($resultado['omitidos'] > 0) {
            $mensaje .= " y {$resultado['omitidos']} duplicado(s) omitido(s)";
        }
        $mensaje .= '.';

        return redirect()->back()->with('success', $mensaje);
    }

    public function plantillaExcel($catalogo)
    {
        $this->autorizar($catalogo, 'crear');

        return $this->excelService->descargarPlantilla($catalogo);
    }

    public function exportarExcel(Request $request, $catalogo)
    {
        $this->autorizar($catalogo, 'ver');

        /** @var Usuario $user */
        $user      = Auth::user();
        $registros = $this->obtenerRegistrosFiltrados($catalogo, $request);

        return $this->excelService->exportar($catalogo, $registros, [
            'es_super'     => $user->esSuperusuario(),
            'institucion'  => $user->institucionActiva()?->nombre ?? 'TODAS LAS INSTITUCIONES',
            'filtro_texto' => trim((string) $request->query('buscar', '')),
        ]);
    }

    public function exportarPdf(Request $request, $catalogo)
    {
        $this->autorizar($catalogo, 'ver');

        /** @var Usuario $user */
        $user        = Auth::user();
        $registros   = $this->obtenerRegistrosFiltrados($catalogo, $request);
        $titulo      = self::TITULOS_PDF[$catalogo] ?? ('CATÁLOGO DE ' . mb_strtoupper($catalogo, 'UTF-8'));
        $esSuper     = $user->esSuperusuario();
        $institucion = $user->institucionActiva()?->nombre ?? 'Todas las instituciones';
        $filtroTexto = trim((string) $request->query('buscar', ''));

        $progId         = (int) $request->query('programa_estudio_id', 0);
        $filtroPrograma = $progId > 0
            ? (ProgramaEstudio::where('id', $progId)->value('nombre') ?? '')
            : '';

        return view('almacen.catalogo.pdf', compact(
            'catalogo',
            'titulo',
            'institucion',
            'esSuper',
            'registros',
            'filtroTexto',
            'filtroPrograma'
        ));
    }

    public function store(Request $request, $catalogo)
    {
        $this->autorizar($catalogo, 'crear');

        $userId = Auth::id();

        if ($catalogo === 'estado') {
            $request->validate(['nombre' => 'required|string|max:60']);
            DB::statement('CALL sp_guardar_estado(?, ?)', [$request->nombre, $userId]);

            return redirect()->back()->with('success', 'Estado registrado correctamente');
        }

        // Si viene por AJAX desde el modal de Bienes sin programa_estudio_id, usamos el primer programa permitido
        if ($request->expectsJson() && ! $request->filled('programa_estudio_id')) {
            $primerPrograma = Auth::user()->idsProgramasPermitidos()[0] ?? null;
            if ($primerPrograma !== null) {
                $request->merge(['programa_estudio_id' => $primerPrograma]);
            }
        }

        $programaDestinoId = $this->resolverProgramaDestino($request);
        if ($programaDestinoId === null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tiene un programa de estudio asignado.'], 422);
            }
            return redirect()->back()->with('aviso_error', 'No tiene un programa de estudio asignado.');
        }

        $request->validate([
            'nombre'      => 'required|string|max:60',
            'observacion' => 'nullable|string|max:200',
        ]);

        $nombreMayus = mb_strtoupper(trim($request->nombre));
        $tabla       = self::TABLAS[$catalogo];

        // Si ya existe en el mismo programa de estudio, lo devolvemos sin duplicar
        $existente = DB::table($tabla)
            ->where('programa_estudio_id', $programaDestinoId)
            ->where('nombre', $nombreMayus)
            ->whereNull('deleted_at')
            ->first();

        if ($existente) {
            if ($request->expectsJson()) {
                return response()->json([
                    'id'      => $existente->id,
                    'nombre'  => $existente->nombre,
                    'message' => 'El registro ya existía y fue seleccionado.',
                ]);
            }
            return redirect()->back()->with('aviso_error', "Ya existe '$nombreMayus' en este programa de estudio.");
        }

        if ($catalogo === 'grupo')  DB::statement('CALL sp_guardar_grupo(?, ?, ?)', [$nombreMayus, $programaDestinoId, $userId]);
        if ($catalogo === 'marca')  DB::statement('CALL sp_guardar_marca(?, ?, ?)', [$nombreMayus, $programaDestinoId, $userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_guardar_tipo(?, ?, ?)', [$nombreMayus, $programaDestinoId, $userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_guardar_modelo(?, ?, ?)', [$nombreMayus, $programaDestinoId, $userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_guardar_sala(?, ?, ?, ?)', [$nombreMayus, $request->observacion ?? null, $programaDestinoId, $userId]);

        if ($request->expectsJson()) {
            $creado = DB::table($tabla)
                ->where('programa_estudio_id', $programaDestinoId)
                ->where('nombre', $nombreMayus)
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->first();

            if (! $creado) {
                return response()->json([
                    'message' => 'Se ejecutó el guardado, pero no se pudo recuperar el registro creado.',
                ], 500);
            }

            return response()->json([
                'id'      => $creado->id,
                'nombre'  => $creado->nombre,
                'message' => ucfirst($catalogo) . ' agregado correctamente.',
            ]);
        }

        return redirect()->back()->with('success', ucfirst($catalogo) . ' registrado correctamente');
    }

    public function update(Request $request, $catalogo, $id)
    {
        $this->autorizar($catalogo, 'editar');
        $this->verificarPertenencia($catalogo, $id);

        $userId = Auth::id();

        if ($catalogo === 'estado') {
            $request->validate(['nombre' => 'required|string|max:60']);
            DB::statement('CALL sp_actualizar_estado(?, ?, ?)', [$id, $request->nombre, $userId]);

            return redirect()->back()->with('success', 'Estado actualizado correctamente');
        }

        $clase    = self::MODELOS[$catalogo];
        $registro = $clase::findOrFail($id);

        $programaDestinoId = $this->resolverProgramaDestino($request, (int) $registro->programa_estudio_id);

        $request->validate([
            'nombre'      => 'required|string|max:60',
            'observacion' => 'nullable|string|max:200',
        ]);

        if ($catalogo === 'grupo')  DB::statement('CALL sp_actualizar_grupo(?, ?, ?, ?)', [$id, $request->nombre, $programaDestinoId, $userId]);
        if ($catalogo === 'marca')  DB::statement('CALL sp_actualizar_marca(?, ?, ?, ?)', [$id, $request->nombre, $programaDestinoId, $userId]);
        if ($catalogo === 'tipo')   DB::statement('CALL sp_actualizar_tipo(?, ?, ?, ?)', [$id, $request->nombre, $programaDestinoId, $userId]);
        if ($catalogo === 'modelo') DB::statement('CALL sp_actualizar_modelo(?, ?, ?, ?)', [$id, $request->nombre, $programaDestinoId, $userId]);
        if ($catalogo === 'sala')   DB::statement('CALL sp_actualizar_sala(?, ?, ?, ?, ?)', [$id, $request->nombre, $request->observacion ?? null, $programaDestinoId, $userId]);

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

    private function obtenerRegistrosFiltrados(string $catalogo, ?Request $request = null): Collection
    {
        $buscar = $request ? mb_strtoupper(trim((string) $request->query('buscar', '')), 'UTF-8') : '';

        if ($catalogo === 'estado') {
            $estados = collect(DB::select('CALL sp_listar_estados()'));
            if ($buscar !== '') {
                $estados = $estados->filter(fn ($e) => str_contains(mb_strtoupper((string) $e->nombre, 'UTF-8'), $buscar))->values();
            }
            return $estados;
        }

        /** @var Usuario $user */
        $user           = Auth::user();
        $institucionId  = ContextoInstitucion::id();
        $idsPermitidos  = $user->idsProgramasPermitidos();
        $tablaProgramas = (new ProgramaEstudio())->getTable();
        $tabla          = self::TABLAS[$catalogo];

        $query = DB::table("$tabla as t")
            ->leftJoin("$tablaProgramas as pe", 'pe.id', '=', 't.programa_estudio_id')
            ->leftJoin('instituciones as inst', 'inst.id', '=', 'pe.institucion_id')
            ->whereNull('t.deleted_at')
            ->select(
                't.*',
                'pe.nombre as programa_estudio_nombre',
                'pe.institucion_id as institucion_id',
                'inst.nombre as institucion_nombre'
            )
            ->orderBy('t.nombre');

        if ($user->esSuperusuario()) {
            if ($institucionId !== null) {
                $query->where('pe.institucion_id', $institucionId);
            }
        } else {
            $query->whereIn('t.programa_estudio_id', $idsPermitidos ?: [-1]);
        }

        if ($request) {
            $filtroInst = (int) $request->query('institucion_id', 0);
            $filtroProg = (int) $request->query('programa_estudio_id', 0);

            if ($filtroInst > 0 && $user->esSuperusuario()) {
                $query->where('pe.institucion_id', $filtroInst);
            }
            if ($filtroProg > 0) {
                $query->where('t.programa_estudio_id', $filtroProg);
            }
            if ($buscar !== '') {
                $query->where(function ($q) use ($buscar, $catalogo) {
                    $q->whereRaw('UPPER(t.nombre) LIKE ?', ["%{$buscar}%"])
                      ->orWhereRaw('UPPER(pe.nombre) LIKE ?', ["%{$buscar}%"]);
                    if ($catalogo === 'sala') {
                        $q->orWhereRaw('UPPER(t.observacion) LIKE ?', ["%{$buscar}%"]);
                    }
                });
            }
        }

        return $query->get();
    }

    private function resolverProgramaDestino(Request $request, ?int $programaActual = null): ?int
    {
        /** @var Usuario $user */
        $user = Auth::user();

        if ($user->esSuperusuario() || $user->tipo_usuario === 'ADMIN_INSTITUCION') {
            $request->validate([
                'programa_estudio_id' => ['required', 'integer', $this->reglaProgramaPermitido()],
            ]);

            return (int) $request->programa_estudio_id;
        }

        if ($request->filled('programa_estudio_id')) {
            $request->validate([
                'programa_estudio_id' => ['integer', $this->reglaProgramaPermitido()],
            ]);

            return (int) $request->programa_estudio_id;
        }

        return $programaActual ?? ($user->idsProgramasPermitidos()[0] ?? null);
    }

    private function puede(string $catalogo, string $accion): bool
    {
        /** @var Usuario $user */
        $user = Auth::user();

        if ($user->esSuperusuario() && $catalogo !== 'estado' && $accion !== 'ver') {
            return false;
        }

        // Si tiene permiso para registrar bienes, permitir crear catálogos rápidos
        if ($accion === 'crear' && $catalogo !== 'estado' && $user->puede('bienes.registrar')) {
            return true;
        }

        return $user->puede("catalogo.$catalogo.$accion");
    }

    private function autorizar(string $catalogo, string $accion): void
    {
        abort_unless($catalogo === 'estado' || isset(self::MODELOS[$catalogo]), 404);
        abort_unless($this->puede($catalogo, $accion), 403, 'Su rol no tiene permiso para realizar esta acción.');
    }

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