<?php

namespace App\Services;

use App\Models\ProgramaEstudio;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogoExcelService
{
    private const TITULOS = [
        'grupo'  => 'CATÁLOGO DE GRUPOS',
        'sala'   => 'CATÁLOGO DE SALAS',
        'tipo'   => 'CATÁLOGO DE TIPOS',
        'marca'  => 'CATÁLOGO DE MARCAS',
        'modelo' => 'CATÁLOGO DE MODELOS',
        'estado' => 'CATÁLOGO DE ESTADOS',
    ];

    private const TABLAS = [
        'grupo'  => 'grupos',
        'sala'   => 'salas',
        'tipo'   => 'tipos',
        'marca'  => 'marcas',
        'modelo' => 'modelos',
        'estado' => 'estados',
    ];

    public function exportar(string $catalogo, Collection $registros, array $meta = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $titulo      = self::TITULOS[$catalogo] ?? ('CATÁLOGO DE ' . mb_strtoupper($catalogo, 'UTF-8'));
        $esSuper     = (bool) ($meta['es_super'] ?? false);
        $esSala      = $catalogo === 'sala';
        $esEstado    = $catalogo === 'estado';

        // Definir columnas según el catálogo
        $columnas = ['N°', 'NOMBRE'];
        if (! $esEstado) {
            if ($esSuper) {
                $columnas[] = 'INSTITUCIÓN';
            }
            $columnas[] = 'PROGRAMA DE ESTUDIO';
            if ($esSala) {
                $columnas[] = 'UBICACIÓN / OBSERVACIÓN';
            }
        }
        $columnas[] = 'FECHA CREACIÓN';
        $columnas[] = 'FECHA MODIFICACIÓN';

        $totalCols = count($columnas);
        $ultimaCol = Coordinate::stringFromColumnIndex($totalCols);

        $sheet->setTitle(mb_substr(ucfirst($catalogo) . 's', 0, 31));
        $sheet->setShowGridLines(true);

        // Banner superior institucional
        $institucionNombre = mb_strtoupper((string) ($meta['institucion'] ?? 'SISTEMA DE INVENTARIO - SISIN'), 'UTF-8');
        $sheet->mergeCells("A1:{$ultimaCol}1");
        $sheet->setCellValue('A1', $institucionNombre);
        $sheet->getStyle("A1:{$ultimaCol}1")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 11, 'bold' => true, 'color' => ['rgb' => 'A7F3D0']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $sheet->mergeCells("A2:{$ultimaCol}2");
        $sheet->setCellValue('A2', $titulo);
        $sheet->getStyle("A2:{$ultimaCol}2")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 15, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $subtitulo = 'Generado el ' . date('d/m/Y H:i') . '   |   Total registros: ' . $registros->count();
        if (! empty($meta['filtro_texto'])) {
            $subtitulo .= '   |   Filtro: "' . mb_strtoupper($meta['filtro_texto'], 'UTF-8') . '"';
        }
        $sheet->mergeCells("A3:{$ultimaCol}3");
        $sheet->setCellValue('A3', $subtitulo);
        $sheet->getStyle("A3:{$ultimaCol}3")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 9.5, 'italic' => true, 'color' => ['rgb' => '374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '10B981']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // Encabezados de tabla (Fila 5)
        $filaHeader = 5;
        foreach ($columnas as $idx => $encabezado) {
            $colLetra = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue("{$colLetra}{$filaHeader}", $encabezado);
        }
        $sheet->getStyle("A{$filaHeader}:{$ultimaCol}{$filaHeader}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);
        $sheet->getRowDimension($filaHeader)->setRowHeight(24);

        // Filas de datos
        $fila = $filaHeader + 1;
        $num  = 1;

        foreach ($registros as $item) {
            $valores = [$num++, mb_strtoupper((string) $item->nombre, 'UTF-8')];

            if (! $esEstado) {
                if ($esSuper) {
                    $valores[] = $item->institucion_nombre ?? 'SIN INSTITUCIÓN';
                }
                $valores[] = $item->programa_estudio_nombre ?? 'GENERAL / N/A';
                if ($esSala) {
                    $valores[] = $item->observacion ?? '-';
                }
            }

            $valores[] = ! empty($item->created_at) ? date('d/m/Y H:i', strtotime((string) $item->created_at)) : '-';
            $valores[] = ! empty($item->updated_at) ? date('d/m/Y H:i', strtotime((string) $item->updated_at)) : '-';

            foreach ($valores as $idx => $val) {
                $colLetra = Coordinate::stringFromColumnIndex($idx + 1);
                $sheet->setCellValue("{$colLetra}{$fila}", $val);
            }

            $bg = ($num % 2 === 0) ? 'FFFFFF' : 'F9FAFB';
            $sheet->getStyle("A{$fila}:{$ultimaCol}{$fila}")->applyFromArray([
                'font'      => ['name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '1F2937']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
            $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$fila}")->getFont()->setBold(true);
            $sheet->getRowDimension($fila)->setRowHeight(20);

            $fila++;
        }

        if ($registros->isEmpty()) {
            $sheet->mergeCells("A{$fila}:{$ultimaCol}{$fila}");
            $sheet->setCellValue("A{$fila}", 'No se encontraron registros para los filtros seleccionados.');
            $sheet->getStyle("A{$fila}:{$ultimaCol}{$fila}")->applyFromArray([
                'font'      => ['name' => 'Calibri', 'size' => 10, 'italic' => true, 'color' => ['rgb' => '6B7280']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
            $sheet->getRowDimension($fila)->setRowHeight(24);
        }

        // Ajuste de ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(7);
        for ($c = 2; $c <= $totalCols; $c++) {
            $colLetra = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetra)->setAutoSize(true);
        }

        $nombreArchivo = 'CATALOGO_' . mb_strtoupper($catalogo, 'UTF-8') . '_' . date('Y_m_d_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {$writer = new Xlsx($spreadsheet);$writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function descargarPlantilla(string $catalogo): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       =$spreadsheet->getActiveSheet();
        $sheet->setTitle('Plantilla_' . ucfirst($catalogo));

        if ($catalogo === 'estado') {
            $headers = ['nombre'];$ejemplo = ['OPERATIVO'];
        } elseif ($catalogo === 'sala') {
            $headers = ['nombre', 'programa_estudio', 'observacion'];$ejemplo = ['LABORATORIO DE CÓMPUTO 01', '', 'PABELLÓN A - 2DO PISO'];
        } else {
            $headers = ['nombre', 'programa_estudio'];
            $ejemplo = ['EJEMPLO ' . mb_strtoupper($catalogo, 'UTF-8'), ''];
        }

        /** @var Usuario $user */$user      = Auth::user();
        $programas = ProgramaEstudio::whereIn('id',$user->idsProgramasPermitidos())
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->pluck('nombre')
            ->values();

        if (count($ejemplo) >= 2 &&$programas->isNotEmpty()) {
            $ejemplo[1] =$programas->first();
        }

        $ultimaCol = Coordinate::stringFromColumnIndex(count($headers));

        foreach ($headers as$idx => $h) {$col = Coordinate::stringFromColumnIndex($idx + 1);$sheet->setCellValue("{$col}1", $h);
            $sheet->setCellValue("{$col}2", $ejemplo[$idx] ?? '');
            $sheet->getColumnDimension($col)->setWidth(34);
        }

        $sheet->getStyle("A1:{$ultimaCol}1")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 11, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '064E3B']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->getStyle("A2:{$ultimaCol}2")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 10, 'italic' => true, 'color' => ['rgb' => '4B5563']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Segunda pestaña con los Programas de Estudio válidos del usuario
        if ($catalogo !== 'estado' &&$programas->isNotEmpty()) {
            $refSheet =$spreadsheet->createSheet();
            $refSheet->setTitle('Programas_Validos');$refSheet->setCellValue('A1', 'PROGRAMAS DE ESTUDIO DISPONIBLES (COPIAR NOMBRE EXACTO)');
            $refSheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
            ]);$refSheet->getColumnDimension('A')->setWidth(55);

            foreach ($programas as$i => $nombreProg) {$refSheet->setCellValue('A' . ($i + 2),$nombreProg);
            }
            $spreadsheet->setActiveSheetIndex(0);
        }

        $nombreArchivo = 'PLANTILLA_' . mb_strtoupper($catalogo, 'UTF-8') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {$writer = new Xlsx($spreadsheet);$writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importar(string $catalogo, UploadedFile$archivo): array
    {
        try {
            $spreadsheet = IOFactory::load($archivo->getRealPath());
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'archivo_excel' => 'No se pudo leer el archivo Excel seleccionado.',
            ]);
        }

        $rows =$spreadsheet->getActiveSheet()->toArray('', true, true, false);
        if (count($rows) < 2) {
            throw ValidationException::withMessages([
                'archivo_excel' => 'El archivo está vacío o no contiene filas de datos.',
            ]);
        }

        // Detectar índices de columnas en la fila 1
        $cabecera = array_map(fn ($v) =>$this->normalizar((string) $v),$rows[0]);
        $idxNombre = array_search('NOMBRE',$cabecera, true);
        if ($idxNombre === false) {$idxNombre = 0;
        }

        $idxPrograma = false;
        foreach (['PROGRAMA_ESTUDIO', 'PROGRAMA DE ESTUDIO', 'PROGRAMA'] as $alias) {$pos = array_search($alias,$cabecera, true);
            if ($pos !== false) {
                $idxPrograma =$pos;
                break;
            }
        }
        if ($idxPrograma === false &&$catalogo !== 'estado' && count($cabecera) >= 2) {$idxPrograma = 1;
        }

        $idxObs = false;
        foreach (['OBSERVACION', 'UBICACION', 'UBICACION / OBSERVACION'] as $alias) {$pos = array_search($alias,$cabecera, true);
            if ($pos !== false) {
                $idxObs =$pos;
                break;
            }
        }
        if ($idxObs === false &&$catalogo === 'sala' && count($cabecera) >= 3) {$idxObs = 2;
        }

        /** @var Usuario $user */$user           = Auth::user();
        $userId         = (int)$user->id;
        $idsPermitidos  =$user->idsProgramasPermitidos();
        $programasMapa  = ProgramaEstudio::whereIn('id',$idsPermitidos)
            ->whereNull('deleted_at')
            ->get(['id', 'nombre'])
            ->mapWithKeys(fn ($p) => [$this->normalizar($p->nombre) => (int)$p->id])
            ->all();

        $programaUnicoId = count($idsPermitidos) === 1 ? (int)$idsPermitidos[0] : null;
        $tabla           = self::TABLAS[$catalogo];$creados         = 0;
        $omitidos        = 0;
        $errores         = [];

        DB::transaction(function () use (
            $rows, $catalogo,$tabla, $userId,$idxNombre, $idxPrograma,$idxObs,
            $programasMapa,$programaUnicoId,
            &$creados, &$omitidos, &$errores
        ) {
            for ($i = 1; $i < count($rows);$i++) {
                $filaNum =$i + 1;
                $row     =$rows[$i];$nombre  = mb_substr(mb_strtoupper(trim((string) ($row[$idxNombre] ?? '')), 'UTF-8'), 0, 60);

                if ($nombre === '') {
                    continue;
                }

                if ($catalogo === 'estado') {$existe = DB::table('estados')
                        ->where('nombre', $nombre)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($existe) {$omitidos++;
                        continue;
                    }

                    DB::statement('CALL sp_guardar_estado(?, ?)', [$nombre, $userId]);$creados++;
                    continue;
                }

                $progRaw    =$idxPrograma !== false ? $this->normalizar((string) ($row[$idxPrograma] ?? '')) : '';$programaId = null;

                if ($progRaw !== '') {$programaId = $programasMapa[$progRaw] ?? null;
                    if (! $programaId) {
                        $errores[] = "Fila {$filaNum}: el programa de estudio '{$row[$idxPrograma]}' no existe o no tiene acceso.";
                        continue;
                    }
                } elseif ($programaUnicoId !== null) {
                    $programaId =$programaUnicoId;
                } else {
                    $errores[] = "Fila {$filaNum}: debe indicar el 'programa_estudio' para '{$nombre}'.";
                    continue;
                }

                $existe = DB::table($tabla)
                    ->where('programa_estudio_id', $programaId)
                    ->where('nombre', $nombre)
                    ->whereNull('deleted_at')
                    ->exists();

                if ($existe) {$omitidos++;
                    continue;
                }

                if ($catalogo === 'grupo') {
                    DB::statement('CALL sp_guardar_grupo(?, ?, ?)', [$nombre, $programaId,$userId]);
                } elseif ($catalogo === 'marca') {
                    DB::statement('CALL sp_guardar_marca(?, ?, ?)', [$nombre, $programaId,$userId]);
                } elseif ($catalogo === 'tipo') {
                    DB::statement('CALL sp_guardar_tipo(?, ?, ?)', [$nombre, $programaId,$userId]);
                } elseif ($catalogo === 'modelo') {
                    DB::statement('CALL sp_guardar_modelo(?, ?, ?)', [$nombre, $programaId,$userId]);
                } elseif ($catalogo === 'sala') {$obsRaw = $idxObs !== false ? trim((string) ($row[$idxObs] ?? '')) : '';$obs    = $obsRaw !== '' ? mb_substr($obsRaw, 0, 200) : null;
                    DB::statement('CALL sp_guardar_sala(?, ?, ?, ?)', [$nombre,$obs, $programaId,$userId]);
                }

                $creados++;
            }

            if (! empty($errores)) {
                throw ValidationException::withMessages(['archivo_excel' => $errores]);
            }
        });

        return [
            'creados'  => $creados,
            'omitidos' => $omitidos,
        ];
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtoupper(trim($texto), 'UTF-8');
        $texto = strtr($texto, [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U',
        ]);

        return preg_replace('/\s+/', ' ', $texto) ?? '';
    }
}