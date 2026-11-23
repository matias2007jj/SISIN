<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Sala;
use App\Models\Usuario;
use App\Services\BienExcelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BienController extends Controller
{
    private const ESTADO_INACTIVO = 6;

    public function index()
    {
        /** @var Usuario $user */
        $user           = Auth::user();
        $institucionId  = $user->institucionActiva()?->id;
        $verInstitucion = $user->veTodaLaInstitucion();
        $idsPermitidos  = $user->idsProgramasPermitidos();

        $bienes = DB::table('bienes as b')
            ->join('salas as s', 'b.sala_id', '=', 's.id')
            ->join('programas_estudio as pe', 's.programa_estudio_id', '=', 'pe.id')
            ->join('instituciones as i', 'pe.institucion_id', '=', 'i.id')
            ->join('grupos as g', 'b.grupo_id', '=', 'g.id')
            ->join('tipos as t', 'b.tipo_id', '=', 't.id')
            ->join('estados as e', 'b.estado_id', '=', 'e.id')
            ->leftJoin('marcas as m', 'b.marca_id', '=', 'm.id')
            ->leftJoin('modelos as mo', 'b.modelo_id', '=', 'mo.id')
            ->whereNull('b.deleted_at')
            ->when($user->esSuperusuario() && $institucionId === null, function ($q) {
                // Vista global
            }, function ($q) use ($idsPermitidos) {
                $q->whereIn('s.programa_estudio_id', $idsPermitidos ?: [-1]);
            })
            ->select(
                'b.id',
                'b.cbi',
                'b.descripcion',
                'b.serie_medida',
                'b.otras_especificaciones',
                'b.sala_id',
                's.nombre as sala_nombre',
                's.programa_estudio_id',
                'pe.nombre as programa_estudio_nombre',
                'i.nombre as institucion_nombre',
                'b.grupo_id',
                'g.nombre as grupo_nombre',
                'b.tipo_id',
                't.nombre as tipo_nombre',
                'b.marca_id',
                'm.nombre as marca_nombre',
                'b.modelo_id',
                'mo.nombre as modelo_nombre',
                'b.estado_id',
                'e.nombre as estado_nombre',
                'b.created_at',
                'b.updated_at'
            )
            ->orderByDesc('b.id')
            ->get();

        $salas   = $this->catalogoPermitido('salas', $idsPermitidos);
        $grupos  = $this->catalogoPermitido('grupos', $idsPermitidos);
        $tipos   = $this->catalogoPermitido('tipos', $idsPermitidos);
        $marcas  = $this->catalogoPermitido('marcas', $idsPermitidos);
        $modelos = $this->catalogoPermitido('modelos', $idsPermitidos);

        $estados = collect(DB::select('CALL sp_listar_estados()'))
            ->where('id', '!=', self::ESTADO_INACTIVO);

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
                // Superusuario global
            }, function ($q) use ($idsPermitidos) {
                $q->whereIn('s.programa_estudio_id', $idsPermitidos ?: [-1]);
            })
            ->orderBy('s.nombre')
            ->get(['s.id', 's.nombre', 's.programa_estudio_id', 'pe.institucion_id']);

        $puedeRegistrar = $user->puede('bienes.registrar') && count($idsPermitidos) > 0;
        $puedeInactivar = $user->puede('bienes.inactivar');

        return view('almacen.bien.index', compact(
            'bienes', 'salas', 'grupos', 'tipos', 'marcas', 'modelos', 'estados',
            'programasFiltro', 'salasFiltro',
            'verInstitucion', 'puedeRegistrar', 'puedeInactivar'
        ));
    }

    public function store(Request $request)
    {
        /** @var Usuario $user */
        $user          = Auth::user();
        $idsPermitidos = $user->idsProgramasPermitidos();

        abort_if(empty($idsPermitidos), 403, 'No tiene programas de estudio asignados para registrar bienes.');

        $sala       = Sala::find($request->input('sala_id'));
        $programaId = (int) ($sala?->programa_estudio_id ?? 0);

        abort_unless(in_array($programaId, $idsPermitidos, true), 403, 'La sala seleccionada no pertenece a sus programas permitidos.');

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
        $bien       = $this->bienPermitido((int) $id);
        $salaNueva  = Sala::find($request->input('sala_id'));
        $programaId = (int) ($salaNueva?->programa_estudio_id ?? $bien->sala?->programa_estudio_id ?? 0);

        abort_unless(Auth::user()->puedeUsarPrograma($programaId), 403, 'No tiene acceso a ese programa de estudio.');

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
        $bien = $this->bienPermitido((int) $id);

        DB::statement('CALL sp_desactivar_bien(?, ?)', [
            $bien->id,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'El bien pasó a estado INACTIVO.');
    }

    public function exportarExcel(Request $request, BienExcelService $excelService)
    {
        [$bienes, $nombrePrograma] = $this->consultaExportacion($request);

        return $excelService->exportar($bienes, [
            'programa' => $nombrePrograma,
        ]);
    }

    public function exportarPdf(Request $request)
    {
        
        [$bienes, $nombrePrograma] = $this->consultaExportacion($request);

        $salasUnicas = $bienes->pluck('sala_nombre')->filter()->unique();
        $tituloSala  = $salasUnicas->count() === 1
            ? (string) $salasUnicas->first()
            : $nombrePrograma;

        return view('almacen.bien.pdf', [
            'bienes'         => $bienes,
            'nombrePrograma' => $nombrePrograma,
            'tituloSala'     => $tituloSala,
            'fechaHoy'       => date('d/m/Y'),
        ]);

        return $this->exportarExcel($request);
    }

    public function importarExcel(Request $request, BienExcelService $excelService)
    {
        /** @var Usuario $user */
        $user          = Auth::user();
        $idsPermitidos = $user->idsProgramasPermitidos();

        abort_unless(
            $user->puede('bienes.registrar') && ! empty($idsPermitidos),
            403,
            'No tiene permisos o programas de estudio asignados para importar bienes.'
        );

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:15360'],
        ], [
            'archivo.required' => 'Debe seleccionar un archivo Excel o CSV.',
            'archivo.mimes'    => 'El archivo debe tener extensión .xlsx, .xls o .csv.',
        ]);

        $programaId = (int) ($request->input('programa_estudio_id') ?: $idsPermitidos[0]);
        abort_unless($user->puedeUsarPrograma($programaId), 403, 'No tiene acceso al programa de estudio seleccionado.');

        $resultado = $excelService->importar($request->file('archivo'), $programaId);

        return redirect()->route('bienes.index')->with(
            'success',
            "Importación completada: {$resultado['creados']} bienes creados y {$resultado['actualizados']} actualizados."
        );
    }

    public function plantillaExcel(BienExcelService $excelService)
    {
        return $excelService->descargarPlantilla();
    }

    private function consultaExportacion(Request $request): array
    {
        /** @var Usuario $user */
        $user          = Auth::user();
        $institucionId = $user->institucionActiva()?->id;
        $idsPermitidos = $user->idsProgramasPermitidos();
        $buscar        = mb_strtoupper(trim((string) $request->input('buscar', '')), 'UTF-8');

        $bienes = DB::table('bienes as b')
            ->join('salas as s', 'b.sala_id', '=', 's.id')
            ->join('programas_estudio as pe', 's.programa_estudio_id', '=', 'pe.id')
            ->join('estados as e', 'b.estado_id', '=', 'e.id')
            ->leftJoin('marcas as m', 'b.marca_id', '=', 'm.id')
            ->leftJoin('modelos as mo', 'b.modelo_id', '=', 'mo.id')
            ->whereNull('b.deleted_at')
            ->when($user->esSuperusuario() && $institucionId === null, function ($q) {
                // Vista global
            }, function ($q) use ($idsPermitidos) {
                $q->whereIn('s.programa_estudio_id', $idsPermitidos ?: [-1]);
            })
            ->when($request->filled('programa_estudio_id'), fn ($q) => $q->where('s.programa_estudio_id', (int) $request->input('programa_estudio_id')))
            ->when($request->filled('sala_id'), fn ($q) => $q->where('b.sala_id', (int) $request->input('sala_id')))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $like = '%' . $buscar . '%';
                $q->where(function ($sub) use ($like) {
                    $sub->whereRaw('UPPER(b.cbi) LIKE ?', [$like])
                        ->orWhereRaw('UPPER(b.descripcion) LIKE ?', [$like])
                        ->orWhereRaw('UPPER(s.nombre) LIKE ?', [$like])
                        ->orWhereRaw('UPPER(COALESCE(m.nombre, "")) LIKE ?', [$like])
                        ->orWhereRaw('UPPER(COALESCE(mo.nombre, "")) LIKE ?', [$like]);
                });
            })
            ->select(
                'b.id',
                'b.cbi',
                'b.descripcion',
                'b.serie_medida',
                'b.otras_especificaciones',
                'b.sala_id',
                's.nombre as sala_nombre',
                's.programa_estudio_id',
                'pe.nombre as programa_estudio_nombre',
                'm.nombre as marca_nombre',
                'mo.nombre as modelo_nombre',
                'b.estado_id',
                'e.nombre as estado_nombre'
            )
            ->orderBy('s.nombre')
            ->orderBy('b.id')
            ->get();

        $nombrePrograma = (string) ($bienes->first()->programa_estudio_nombre ?? '');
        if ($nombrePrograma === '' && ! empty($idsPermitidos)) {
            $nombrePrograma = (string) DB::table('programas_estudio')
                ->where('id', $idsPermitidos[0])
                ->value('nombre');
        }

        return [$bienes, $nombrePrograma ?: 'OFICINA DE CÓMPUTO'];
    }

    private function catalogoPermitido(string $tabla, array $idsPermitidos)
    {
        if (empty($idsPermitidos)) {
            return collect();
        }

        return DB::table("$tabla as t")
            ->join('programas_estudio as pe', 'pe.id', '=', 't.programa_estudio_id')
            ->whereIn('t.programa_estudio_id', $idsPermitidos)
            ->whereNull('t.deleted_at')
            ->orderBy('t.nombre')
            ->get(['t.id', 't.nombre', 't.programa_estudio_id', 'pe.nombre as programa_nombre']);
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

    private function bienPermitido(int $id): Bien
    {
        $bien = Bien::with('sala')->findOrFail($id);

        abort_unless(
            Auth::user()->puedeUsarPrograma((int) $bien->sala?->programa_estudio_id),
            404
        );

        return $bien;
    }
}