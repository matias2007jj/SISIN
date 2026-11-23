<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Estado;
use App\Models\Grupo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Sala;
use App\Models\Tipo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BienExcelService
{
    private const ESTADO_INACTIVO   = 6;
    private const FILA_ENCABEZADOS  = 13;
    private const FILA_INICIO_DATOS = 14;

    /**
     * Firma estructural exacta de las 12 columnas (A:L) del formato oficial.
     */
    private const COLUMNAS_ESPERADAS = [
        'A' => ['N°', 'Nº'],
        'B' => ['INV 2025', '2025'],
        'C' => ['INV 2024', '2024'],
        'D' => ['INV 2023', '2023'],
        'E' => ['CODIGO PATRIMONIAL', 'COD. PATRI.'],
        'F' => ['CBI'],
        'G' => ['DESCRIPCIÓN DEL BIEN', 'DESCRIPCION DEL BIEN'],
        'H' => ['MARCA'],
        'I' => ['MODELO'],
        'J' => ['SERIE/MEDIDA', 'SERIE / MEDIDA'],
        'K' => ['ESTADO'],
        'L' => ['OTRAS ESPECIFICACIONES'],
    ];

    /**
     * Valida estrictamente el formato oficial en todas las pestañas e importa los bienes.
     */
    public function importar(UploadedFile $archivo, int $programaId): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($archivo->getRealPath());

        $hojasValidadas    = [];
        $erroresEstructura = [];

        foreach ($spreadsheet->getAllSheets() as $hoja) {
            $tituloHoja = $hoja->getTitle();
            $filaHeader = $this->detectarFilaEncabezados($hoja);

            if ($filaHeader === null) {
                $erroresEstructura[] = "Hoja '{$tituloHoja}': no se encontró la fila oficial de encabezados (N° hasta OTRAS ESPECIFICACIONES).";
                continue;
            }

            // Verificar que no existan columnas no permitidas a la derecha de L
            $ultimaColumnaPermitida = Coordinate::columnIndexFromString('L');
            $ultimaColumnaArchivo   = Coordinate::columnIndexFromString($hoja->getHighestDataColumn($filaHeader));

            if ($ultimaColumnaArchivo > $ultimaColumnaPermitida) {
                $erroresEstructura[] = "Hoja '{$tituloHoja}': contiene columnas adicionales a la derecha de la columna L.";
                continue;
            }

            // Verificar firma institucional superior (DOCENTE RESPONSABLE y SALA)
            $infoCabecera = $this->extraerCabeceraInstitucional($hoja, $filaHeader);
            if (! $infoCabecera['tieneDocente'] || ! $infoCabecera['tieneSala']) {
                $erroresEstructura[] = "Hoja '{$tituloHoja}': faltan las etiquetas institucionales de cabecera (DOCENTE RESPONSABLE / SALA).";
                continue;
            }

            $salaNombre = $infoCabecera['sala'] !== ''
                ? $infoCabecera['sala']
                : mb_strtoupper(trim($tituloHoja), 'UTF-8');

            $hojasValidadas[] = [
                'hoja'       => $hoja,
                'titulo'     => $tituloHoja,
                'filaInicio' => $filaHeader + 1,
                'salaNombre' => mb_substr($salaNombre, 0, 60),
            ];
        }

        if (! empty($erroresEstructura) || empty($hojasValidadas)) {
            throw ValidationException::withMessages([
                'archivo' => array_merge(
                    ['El archivo no cumple con el formato oficial de inventario:'],
                    $erroresEstructura
                ),
            ]);
        }

        $creados      = 0;
        $actualizados = 0;
        $erroresFilas = [];
        $usuarioId    = Auth::id();

        DB::transaction(function () use (
            $hojasValidadas, $programaId, $usuarioId,
            &$creados, &$actualizados, &$erroresFilas
        ) {
            $grupoPorDefecto = Grupo::where('programa_estudio_id', $programaId)->whereNull('deleted_at')->orderBy('id')->first()
                ?? Grupo::firstOrCreate(['nombre' => 'GENERAL', 'programa_estudio_id' => $programaId]);

            $tipoPorDefecto = Tipo::where('programa_estudio_id', $programaId)->whereNull('deleted_at')->orderBy('id')->first()
                ?? Tipo::firstOrCreate(['nombre' => 'EQUIPO / MOBILIARIO', 'programa_estudio_id' => $programaId]);

            $estadoPorDefecto = Estado::where('id', '!=', self::ESTADO_INACTIVO)->orderBy('id')->first();

            foreach ($hojasValidadas as $itemHoja) {
                /** @var Worksheet $hoja */
                $hoja       = $itemHoja['hoja'];
                $tituloHoja = $itemHoja['titulo'];
                $filaInicio = $itemHoja['filaInicio'];
                $salaNombre = $itemHoja['salaNombre'];
                $ultimaFila = $hoja->getHighestDataRow();

                $sala = Sala::firstOrCreate([
                    'nombre'              => $salaNombre,
                    'programa_estudio_id' => $programaId,
                ]);

                for ($fila = $filaInicio; $fila <= $ultimaFila; $fila++) {
                    $colA        = trim((string) $hoja->getCell("A{$fila}")->getValue());
                    $cbi         = mb_strtoupper(trim((string) $hoja->getCell("F{$fila}")->getValue()), 'UTF-8');
                    $descripcion = mb_strtoupper(trim((string) $hoja->getCell("G{$fila}")->getValue()), 'UTF-8');

                    // Detener al llegar a la leyenda final de estado o nota legal
                    if (
                        str_starts_with($colA, 'ESTADO') ||
                        str_starts_with($colA, 'NOTA') ||
                        str_starts_with($descripcion, 'ESTADO:') ||
                        str_starts_with($descripcion, 'NOTA')
                    ) {
                        break;
                    }

                    // Ignorar filas vacías, separadores de bloque (ej. "MAQUINA 001" en D) o ítems sin CBI ("S/N", "S/C")
                    if ($cbi === '' || in_array($cbi, ['S/N', 'S/C', '-'], true)) {
                        continue;
                    }

                    if (mb_strlen($cbi) > 10) {
                        $erroresFilas[] = "Hoja '{$tituloHoja}', fila {$fila}: el CBI '{$cbi}' supera los 10 caracteres.";
                        continue;
                    }

                    if ($descripcion === '') {
                        $erroresFilas[] = "Hoja '{$tituloHoja}', fila {$fila}: la DESCRIPCIÓN DEL BIEN (columna G) es obligatoria.";
                        continue;
                    }
                    $descripcion = mb_substr($descripcion, 0, 100);

                    $marca  = $this->obtenerOCrearCatalogoPrograma(Marca::class, (string) $hoja->getCell("H{$fila}")->getValue(), $programaId);
                    $modelo = $this->obtenerOCrearCatalogoPrograma(Modelo::class, (string) $hoja->getCell("I{$fila}")->getValue(), $programaId);

                    $serieRaw = mb_strtoupper(trim((string) $hoja->getCell("J{$fila}")->getValue()), 'UTF-8');
                    $serie    = ($serieRaw === '' || in_array($serieRaw, ['S/SERIE', 'S/N', '-'], true))
                        ? null
                        : mb_substr($serieRaw, 0, 100);

                    $otrasRaw = mb_strtoupper(trim((string) $hoja->getCell("L{$fila}")->getValue()), 'UTF-8');
                    $otras    = $otrasRaw !== '' ? mb_substr($otrasRaw, 0, 100) : null;

                    $estadoId = $this->resolverEstado($hoja->getCell("K{$fila}")->getValue(), $estadoPorDefecto?->id);
                    if (! $estadoId) {
                        $erroresFilas[] = "Hoja '{$tituloHoja}', fila {$fila}: estado inválido en columna K.";
                        continue;
                    }

                    $bienExistente = Bien::with('sala')
                        ->where('cbi', $cbi)
                        ->whereNull('deleted_at')
                        ->first();

                    if ($bienExistente) {
                        if ((int) $bienExistente->sala?->programa_estudio_id !== $programaId) {
                            $erroresFilas[] = "Hoja '{$tituloHoja}', fila {$fila}: el CBI '{$cbi}' ya pertenece a otro programa de estudio.";
                            continue;
                        }

                        DB::statement('CALL sp_actualizar_bien(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                            $bienExistente->id,
                            $sala->id,
                            $bienExistente->grupo_id ?: $grupoPorDefecto->id,
                            $cbi,
                            $bienExistente->tipo_id ?: $tipoPorDefecto->id,
                            $descripcion,
                            $marca?->id,
                            $modelo?->id,
                            $serie,
                            $estadoId,
                            $otras,
                            $usuarioId,
                        ]);
                        $actualizados++;
                    } else {
                        DB::statement('CALL sp_guardar_bien(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                            $sala->id,
                            $grupoPorDefecto->id,
                            $cbi,
                            $tipoPorDefecto->id,
                            $descripcion,
                            $marca?->id,
                            $modelo?->id,
                            $serie,
                            $estadoId,
                            $otras,
                            $usuarioId,
                        ]);
                        $creados++;
                    }
                }
            }

            if (! empty($erroresFilas)) {
                throw ValidationException::withMessages(['archivo' => $erroresFilas]);
            }
        });

        return [
            'creados'      => $creados,
            'actualizados' => $actualizados,
            'total'        => $creados + $actualizados,
        ];
    }

    /**
     * Exporta los bienes conservando el diseño completo: logos, recuadro de título,
     * tabla de datos, leyenda de estados, nota legal y bloque de 3 firmas al pie.
     */
    public function exportar(iterable $bienes, array $metadatos = []): StreamedResponse
    {
        $spreadsheet = IOFactory::load($this->rutaPlantilla());
        $hoja        = $spreadsheet->getSheet(0);
        $listaBienes = collect($bienes);

        foreach ($hoja->getMergeCells() as $rangoExistente) {
            [$inicioEx] = Coordinate::rangeBoundaries($rangoExistente);
            if ($inicioEx[1] >= self::FILA_INICIO_DATOS) {
                $hoja->unmergeCells($rangoExistente);
            }
        }

        $this->renderizarEncabezadoSuperior($hoja, $listaBienes, $metadatos);

        $filaActual      = self::FILA_INICIO_DATOS;
        $rangoEstiloBase = 'A' . self::FILA_INICIO_DATOS . ':L' . self::FILA_INICIO_DATOS;
        $altoFilaBase    = $hoja->getRowDimension(self::FILA_INICIO_DATOS)->getRowHeight();
        $item            = 1;

        foreach ($listaBienes as $b) {
            if ($filaActual > self::FILA_INICIO_DATOS) {
                $hoja->insertNewRowBefore($filaActual, 1);
                $hoja->duplicateStyle(
                    $hoja->getStyle($rangoEstiloBase),
                    "A{$filaActual}:L{$filaActual}"
                );
                if ($altoFilaBase > 0) {
                    $hoja->getRowDimension($filaActual)->setRowHeight($altoFilaBase);
                }
            }

            $hoja->setCellValue("A{$filaActual}", $item++);
            $hoja->setCellValue("B{$filaActual}", 'S/N');
            $hoja->setCellValue("C{$filaActual}", 'S/N');
            $hoja->setCellValue("D{$filaActual}", 'S/N');
            $hoja->setCellValue("E{$filaActual}", 'S/N');
            $hoja->setCellValue("F{$filaActual}", $b->cbi ?? '');
            $hoja->setCellValue("G{$filaActual}", $b->descripcion ?? '');
            $hoja->setCellValue("H{$filaActual}", $b->marca_nombre ?: 'S/M');
            $hoja->setCellValue("I{$filaActual}", $b->modelo_nombre ?: 'S/M');
            $hoja->setCellValue("J{$filaActual}", $b->serie_medida ?: 'S/SERIE');
            $hoja->setCellValue("K{$filaActual}", $b->estado_id ?? 2);
            $hoja->setCellValue("L{$filaActual}", $b->otras_especificaciones ?? '');

            $filaActual++;
        }

        $this->renderizarPieYFirmas($hoja, max($filaActual, self::FILA_INICIO_DATOS + 1));

        $nombreArchivo = 'INVENTARIO_BIENES_' . date('Y_m_d_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Busca entre las filas 7 y 16 la fila exacta que coincide con las 12 columnas oficiales.
     */
    private function detectarFilaEncabezados(Worksheet $hoja): ?int
    {
        for ($fila = 7; $fila <= 16; $fila++) {
            $coincide = true;

            foreach (self::COLUMNAS_ESPERADAS as $columna => $titulosValidos) {
                $valorCelda = $this->normalizarTexto((string) $hoja->getCell("{$columna}{$fila}")->getValue());
                $validos    = array_map(fn ($t) => $this->normalizarTexto($t), $titulosValidos);

                if (! in_array($valorCelda, $validos, true)) {
                    $coincide = false;
                    break;
                }
            }

            if ($coincide) {
                return $fila;
            }
        }

        return null;
    }

    /**
     * Verifica la existencia de las etiquetas institucionales y extrae el nombre de la Sala.
     */
    private function extraerCabeceraInstitucional(Worksheet $hoja, int $filaHeader): array
    {
        $tieneDocente = false;
        $tieneSala    = false;
        $salaValor    = '';

        for ($r = 1; $r < $filaHeader; $r++) {
            for ($c = 1; $c <= 12; $c++) {
                $col   = Coordinate::stringFromColumnIndex($c);
                $texto = $this->normalizarTexto((string) $hoja->getCell("{$col}{$r}")->getValue());

                if ($texto === 'DOCENTE RESPONSABLE') {
                    $tieneDocente = true;
                }

                if ($texto === 'SALA') {
                    $tieneSala = true;
                    // Buscar el valor de la sala en las celdas inmediatas a la derecha (ej. J11)
                    for ($cNext = $c + 1; $cNext <= 12; $cNext++) {
                        $colNext = Coordinate::stringFromColumnIndex($cNext);
                        $val     = mb_strtoupper(trim((string) $hoja->getCell("{$colNext}{$r}")->getValue()), 'UTF-8');
                        if ($val !== '') {
                            $salaValor = $val;
                            break;
                        }
                    }
                }
            }
        }

        return [
            'tieneDocente' => $tieneDocente,
            'tieneSala'    => $tieneSala,
            'sala'         => $salaValor,
        ];
    }

    private function renderizarEncabezadoSuperior(Worksheet $hoja, $listaBienes, array $metadatos): void
    {
        $this->descombinarSiSolapa($hoja, 'H2:I5');
        $hoja->mergeCells('H2:I5');
        $hoja->setCellValue('H2', "I.E.S.T.P.\nJULIO CESAR\nTELLO");
        $hoja->getStyle('H2:I5')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $salasUnicas = $listaBienes->pluck('sala_nombre')->filter()->unique();
        $tituloSala  = $salasUnicas->count() === 1
            ? $salasUnicas->first()
            : ($metadatos['programa'] ?? 'OFICINA DE CÓMPUTO');

        $this->descombinarSiSolapa($hoja, 'J2:L5');
        $hoja->mergeCells('J2:L5');
        $hoja->setCellValue('J2', 'INVENTARIO DE ' . mb_strtoupper((string) $tituloSala, 'UTF-8'));
        $hoja->getStyle('J2:L5')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 13, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '595959'],
                ],
            ],
        ]);

        $hoja->setCellValue('G11', date('d/m/Y'));
        $hoja->setCellValue('J11', $tituloSala);
    }

    private function renderizarPieYFirmas(Worksheet $hoja, int $filaInicioPie): void
    {
        $maxFilaPrevia = $hoja->getHighestRow();
        for ($r = $filaInicioPie; $r <= $maxFilaPrevia; $r++) {
            for ($c = 1; $c <= 12; $c++) {
                $col = Coordinate::stringFromColumnIndex($c);
                $hoja->setCellValue("{$col}{$r}", null);
            }
        }

        $filaLeyenda = $filaInicioPie + 2;
        $hoja->mergeCells("F{$filaLeyenda}:L{$filaLeyenda}");
        $hoja->setCellValue(
            "F{$filaLeyenda}",
            'ESTADO: 1= NUEVO, 2=BUENO, 3=REGULAR, 4= MALO   SIN MARCA=S/M, SIN NUMERO= S/N, SIN SERIE =S/SERIE'
        );
        $hoja->getStyle("F{$filaLeyenda}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 9, 'bold' => false],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $filaNotaInicio = $filaInicioPie + 4;
        $filaNotaFin    = $filaInicioPie + 6;
        $hoja->mergeCells("F{$filaNotaInicio}:L{$filaNotaFin}");
        $hoja->setCellValue(
            "F{$filaNotaInicio}",
            'NOTA : En caso de robo, extravío o deterioro, el servidor deberá dar cuenta a su jefe adjuntando la correspondiente denuncia policial del hecho, caso contrario asumirá la responsabilidad de reparación y/o reposición del bien.'
        );
        $hoja->getStyle("F{$filaNotaInicio}:L{$filaNotaFin}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $filaFirmaLatIni = $filaInicioPie + 10;
        $filaFirmaLatFin = $filaInicioPie + 13;

        $hoja->mergeCells("B{$filaFirmaLatIni}:F{$filaFirmaLatFin}");
        $hoja->setCellValue(
            "B{$filaFirmaLatIni}",
            "----------------------------------------------\n" .
            "Gutierrez Chura Luis Alberto\n" .
            "Asistente de Computación e informática\n" .
            "Turno Nocturno"
        );
        $hoja->getStyle("B{$filaFirmaLatIni}:F{$filaFirmaLatFin}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $hoja->mergeCells("J{$filaFirmaLatIni}:L{$filaFirmaLatFin}");
        $hoja->setCellValue(
            "J{$filaFirmaLatIni}",
            "-----------------------------------------------\n" .
            "Segundo Teodulo Sanchez Rodriguez\n" .
            "Asistente de Computación e informática\n" .
            "Turno Diurno"
        );
        $hoja->getStyle("J{$filaFirmaLatIni}:L{$filaFirmaLatFin}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $filaFirmaCenIni = $filaInicioPie + 14;
        $filaFirmaCenFin = $filaInicioPie + 16;
        $hoja->mergeCells("G{$filaFirmaCenIni}:I{$filaFirmaCenFin}");
        $hoja->setCellValue(
            "G{$filaFirmaCenIni}",
            "-------------------------------------------\n" .
            "Mg. Miguel Mauro Culi Janampa\n" .
            "Coordinador del Area Académica Computación e Informática"
        );
        $hoja->getStyle("G{$filaFirmaCenIni}:I{$filaFirmaCenFin}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);
    }

    private function descombinarSiSolapa(Worksheet $hoja, string $rangoObjetivo): void
    {
        [$inicioObj, $finObj] = Coordinate::rangeBoundaries($rangoObjetivo);

        foreach ($hoja->getMergeCells() as $rangoExistente) {
            [$inicioEx, $finEx] = Coordinate::rangeBoundaries($rangoExistente);
            $solapaCols  = $inicioObj[0] <= $finEx[0] && $finObj[0] >= $inicioEx[0];
            $solapaFilas = $inicioObj[1] <= $finEx[1] && $finObj[1] >= $inicioEx[1];

            if ($solapaCols && $solapaFilas) {
                $hoja->unmergeCells($rangoExistente);
            }
        }
    }

    public function descargarPlantilla()
    {
        return response()->download(
            $this->rutaPlantilla(),
            'PLANTILLA_INVENTARIO_OFICIAL.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    private function rutaPlantilla(): string
    {
        $ruta = storage_path('app/plantillas/formato_inventario.xlsx');
        abort_unless(file_exists($ruta), 500, 'No se encontró la plantilla oficial en storage/app/plantillas/formato_inventario.xlsx.');

        return $ruta;
    }

    private function normalizarTexto(string $texto): string
    {
        $texto = preg_replace('/\s+/', ' ', trim($texto));
        return mb_strtoupper($texto, 'UTF-8');
    }

    private function resolverEstado(mixed $valorCelda, ?int $defaultId): ?int
    {
        $raw = mb_strtoupper(trim((string) $valorCelda), 'UTF-8');
        if ($raw === '') {
            return $defaultId;
        }

        if (is_numeric($raw)) {
            $id = (int) $raw;
            if ($id !== self::ESTADO_INACTIVO && Estado::where('id', $id)->whereNull('deleted_at')->exists()) {
                return $id;
            }
        }

        $estado = Estado::whereRaw('UPPER(nombre) = ?', [$raw])
            ->where('id', '!=', self::ESTADO_INACTIVO)
            ->whereNull('deleted_at')
            ->first();

        return $estado?->id ?? $defaultId;
    }

    private function obtenerOCrearCatalogoPrograma(string $modeloClase, string $nombreRaw, int $programaId): mixed
    {
        $nombre = mb_substr(mb_strtoupper(trim($nombreRaw), 'UTF-8'), 0, 60);

        if ($nombre === '' || in_array($nombre, ['S/M', 'S/MARCA', 'S/MODELO', 'S/N', '-'], true)) {
            return null;
        }

        return $modeloClase::firstOrCreate([
            'nombre'              => $nombre,
            'programa_estudio_id' => $programaId,
        ]);
    }
}