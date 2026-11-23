<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Usuario;
use App\Services\BienExcelService;
use App\Support\ContextoInstitucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BienController extends Controller
{
    private const ESTADO_INACTIVO = 6;

    public function index()
    {
        /** @var Usuario $user */
        $user           = Auth::user();
        $institucionId  = $user->institucionActiva()?->id;
        $programaId     = ContextoInstitucion::programaId();
        $verInstitucion = $user->veTodaLaInstitucion();
        $idsPermitidos  = $user->idsProgramasPermitidos();

        $bienes = DB::select('CALL sp_listar_bienes(?, ?, ?)', [
            $programaId,
            $institucionId,
            $verInstitucion ? 1 : 0,
        ]);

        // Listas para los modales (del programa activo)
        $salas   = $programaId ? DB::select('CALL sp_listar_salas(?)', [$programaId]) : [];
        $grupos  = $programaId ? DB::select('CALL sp_listar_grupos(?, 0)', [$programaId]) : [];
        $tipos   = $programaId ? DB::select('CALL sp_listar_tipos(?)', [$programaId]) : [];
        $marcas  = $programaId ? DB::select('CALL sp_listar_marcas(?)', [$programaId]) : [];
        $modelos = $programaId ? DB::select('CALL sp_listar_modelos(?)', [$programaId]) : [];

        $estados = collect(DB::select('CALL sp_listar_estados()'))
            ->where('id', '!=', self::ESTADO_INACTIVO);

        // Listas para los filtros de la tabla
        $programasFiltro = DB::table('programas_estudio as pe')
            ->whereIn('pe.id', $idsPermitidos)
            ->whereNull('pe.deleted_at')
            ->when($user->esSuperusuario() && $institucionId !== null, fn ($q) => $q->where('pe.institucion_id', $institucionId))
            ->orderBy('pe.nombre')
            ->get(['pe.id', 'pe.nombre', 'pe.institucion_id']);

        $salasFiltro = DB::table('salas as s')
            ->join('programas_estudio as pe', 'pe.id', '=', 's.programa_estudio_id')
            ->whereNull('s.deleted_at')
            ->whereNull('pe.deleted_at')
            ->when($user->esSuperusuario() && $institucionId === null, function ($q) {
                // Superusuario en vista global: todas las salas
            }, function ($q) use ($verInstitucion, $idsPermitidos, $programaId) {
                if ($verInstitucion) {
                    $q->whereIn('s.programa_estudio_id', $idsPermitidos);
                } elseif ($programaId !== null) {
                    $q->where('s.programa_estudio_id', $programaId);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->orderBy('s.nombre')
            ->get(['s.id', 's.nombre', 's.programa_estudio_id', 'pe.institucion_id']);

        $puedeRegistrar = $user->puede('bienes.registrar') && $programaId !== null;
        $puedeInactivar = $user->puede('bienes.inactivar');

        return view('almacen.bien.index', compact(
            'bienes', 'salas', 'grupos', 'tipos', 'marcas', 'modelos', 'estados',
            'programasFiltro', 'salasFiltro',
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

        DB::statement('CALL sp_desactivar_bien(?, ?)', [
            $bien->id,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'El bien pasó a estado INACTIVO.');
    }

    public function importarExcel(Request $request, BienExcelService $excelService)
    {
        $programaId = ContextoInstitucion::programaId();

        if ($programaId === null) {
            return redirect()->route('bienes.index')
                ->with('aviso_error', 'Seleccione un programa de estudio activo antes de importar.');
        }

        $request->validate([
            'archivo' => [
                'required',
                'file',
                'mimes:xlsx',
                'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'max:10240',
            ],
        ], [
            'archivo.required'  => 'Debe seleccionar un archivo Excel (.xlsx).',
            'archivo.mimes'     => 'Solo se permite importar archivos en formato oficial .xlsx.',
            'archivo.mimetypes' => 'El archivo seleccionado no es un documento Excel (.xlsx) válido.',
            'archivo.max'       => 'El archivo no debe superar los 10 MB.',
        ]);

        try {
            $resumen = $excelService->importar($request->file('archivo'), $programaId);
        } catch (ValidationException $e) {
            $detalle = collect($e->errors())->flatten()->implode(' | ');
            return redirect()->route('bienes.index')->with('aviso_error', $detalle);
        }

        return redirect()->route('bienes.index')->with(
            'success',
            "Importación exitosa: {$resumen['creados']} nuevos y {$resumen['actualizados']} actualizados (Total: {$resumen['total']})."
        );
    }

    public function plantillaExcel(BienExcelService $excelService)
    {
        return $excelService->descargarPlantilla();
    }

    public function exportarExcel(BienExcelService $excelService)
    {
        /** @var Usuario $user */
        $user           = Auth::user();
        $institucion    = $user->institucionActiva();
        $institucionId  = $institucion?->id;
        $programaId     = ContextoInstitucion::programaId();
        $verInstitucion = $user->veTodaLaInstitucion();

        $bienes = DB::select('CALL sp_listar_bienes(?, ?, ?)', [
            $programaId,
            $institucionId,
            $verInstitucion ? 1 : 0,
        ]);

        return $excelService->exportar($bienes, [
            'institucion' => $institucion?->nombre ?? 'TODAS LAS INSTITUCIONES',
            'programa'    => $programaId ? DB::table('programas_estudio')->where('id', $programaId)->value('nombre') : 'TODOS LOS PROGRAMAS',
        ]);
    }

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

    private function delProgramaActivo(int $id, ?int $programaId): Bien
    {
        abort_if($programaId === null, 403, 'No tiene un programa de estudio activo.');

        $bien = Bien::with('sala')->findOrFail($id);

        abort_unless((int) $bien->sala?->programa_estudio_id === $programaId, 404);

        return $bien;
    }
}