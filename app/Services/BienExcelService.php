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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BienExcelService
{
    private const ESTADO_INACTIVO = 6;

    public function importar(UploadedFile $archivo, int $programaId): array
    {
        $filasJson = request()->input('filas_json');
        $registros = [];

        if (is_string($filasJson) && trim($filasJson) !== '') {
            $decodificado = json_decode($filasJson, true);
            if (! is_array($decodificado) || empty($decodificado)) {
                throw ValidationException::withMessages([
                    'archivo' => 'No se encontraron registros válidos con CBI en el archivo seleccionado.',
                ]);
            }
            $registros = $decodificado;
        } else {
            $extension = strtolower((string) $archivo->getClientOriginalExtension());
            $registros = $extension === 'csv'
                ? $this->parsearCsvNativo($archivo)
                : $this->parsearExcelServidor($archivo);
        }

        if (empty($registros)) {
            throw ValidationException::withMessages([
                'archivo' => 'No se encontraron registros con CBI válido para importar.',
            ]);
        }

        $creados      = 0;
        $actualizados = 0;
        $erroresFilas = [];
        $usuarioId    = Auth::id();

        DB::transaction(function () use (
            $registros, $programaId, $usuarioId,
            &$creados, &$actualizados, &$erroresFilas
        ) {
            $grupoPorDefecto = Grupo::where('programa_estudio_id', $programaId)->whereNull('deleted_at')->orderBy('id')->first()
                ?? Grupo::firstOrCreate(['nombre' => 'GENERAL', 'programa_estudio_id' => $programaId]);

            $tipoPorDefecto = Tipo::where('programa_estudio_id', $programaId)->whereNull('deleted_at')->orderBy('id')->first()
                ?? Tipo::firstOrCreate(['nombre' => 'EQUIPO / MOBILIARIO', 'programa_estudio_id' => $programaId]);

            $estadoPorDefecto = Estado::where('id', '!=', self::ESTADO_INACTIVO)->orderBy('id')->first();
            $cacheSalas       = [];

            foreach ($registros as $reg) {
                $hoja        = (string) ($reg['hoja'] ?? 'INVENTARIO');
                $fila        = (int) ($reg['fila'] ?? 0);
                $salaNombre  = mb_substr(mb_strtoupper(trim((string) ($reg['sala'] ?? $hoja)), 'UTF-8'), 0, 60);
                $cbi         = mb_strtoupper(trim((string) ($reg['cbi'] ?? '')), 'UTF-8');
                $descripcion = mb_strtoupper(trim((string) ($reg['descripcion'] ?? '')), 'UTF-8');

                if ($cbi === '' || in_array($cbi, ['S/N', 'S/C', '-'], true)) {
                    continue;
                }

                if (mb_strlen($cbi) > 10) {
                    $erroresFilas[] = "Hoja '{$hoja}', fila {$fila}: el CBI '{$cbi}' supera los 10 caracteres.";
                    continue;
                }

                if ($descripcion === '') {
                    $erroresFilas[] = "Hoja '{$hoja}', fila {$fila}: la DESCRIPCIÓN DEL BIEN es obligatoria.";
                    continue;
                }
                $descripcion = mb_substr($descripcion, 0, 100);

                if ($salaNombre === '') {
                    $salaNombre = 'SALA GENERAL';
                }

                if (! isset($cacheSalas[$salaNombre])) {
                    $cacheSalas[$salaNombre] = Sala::firstOrCreate([
                        'nombre'              => $salaNombre,
                        'programa_estudio_id' => $programaId,
                    ]);
                }
                $sala = $cacheSalas[$salaNombre];

                $marca  = $this->obtenerOCrearCatalogoPrograma(Marca::class, (string) ($reg['marca'] ?? ''), $programaId);
                $modelo = $this->obtenerOCrearCatalogoPrograma(Modelo::class, (string) ($reg['modelo'] ?? ''), $programaId);

                $serieRaw = mb_strtoupper(trim((string) ($reg['serie'] ?? '')), 'UTF-8');
                $serie    = ($serieRaw === '' || in_array($serieRaw, ['S/SERIE', 'S/N', '-'], true))
                    ? null
                    : mb_substr($serieRaw, 0, 100);

                $otrasRaw = mb_strtoupper(trim((string) ($reg['otras'] ?? '')), 'UTF-8');
                $otras    = $otrasRaw !== '' ? mb_substr($otrasRaw, 0, 100) : null;

                $estadoId = $this->resolverEstado($reg['estado'] ?? '', $estadoPorDefecto?->id);
                if (! $estadoId) {
                    $erroresFilas[] = "Hoja '{$hoja}', fila {$fila}: estado inválido.";
                    continue;
                }

                $bienExistente = Bien::with('sala')
                    ->where('cbi', $cbi)
                    ->whereNull('deleted_at')
                    ->first();

                if ($bienExistente) {
                    if ((int) $bienExistente->sala?->programa_estudio_id !== $programaId) {
                        $erroresFilas[] = "Hoja '{$hoja}', fila {$fila}: el CBI '{$cbi}' ya pertenece a otro programa de estudio.";
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

    public function exportar(iterable $bienes, array $metadatos = []): StreamedResponse
    {
        $listaBienes   = collect($bienes);
        $nombreArchivo = 'INVENTARIO_BIENES_' . date('Y_m_d_His') . '.xlsx';

        return $this->generarDesdePlantillaOficial($listaBienes, $metadatos, $nombreArchivo);
    }

    public function descargarPlantilla(): StreamedResponse
    {
        return $this->generarDesdePlantillaOficial(
            collect([]),
            ['programa' => 'OFICINA DE CÓMPUTO'],
            'PLANTILLA_INVENTARIO_OFICIAL.xlsx'
        );
    }

    private function generarDesdePlantillaOficial($listaBienes, array $metadatos, string $nombreArchivo): StreamedResponse
    {
        $rutaPlantilla = storage_path('app/plantillas/formato_inventario.xlsx');
        $spreadsheet   = null;

        if (is_file($rutaPlantilla)) {
            try {
                $spreadsheet = IOFactory::load($rutaPlantilla);
            } catch (\Throwable $e) {
                $spreadsheet = null;
            }
        }

        // Si no existe (o no se puede leer) la plantilla oficial, se construye el mismo formato desde cero.
        $spreadsheet ??= $this->crearFormatoBase();
        $sheet         = $spreadsheet->getActiveSheet();

        // Vista Normal (quita las marcas de agua grises "Página 1 / Página 2" y líneas azules)
        $sheet->getSheetView()->setView(SheetView::SHEETVIEW_NORMAL);

        $salasUnicas = $listaBienes->pluck('sala_nombre')->filter()->unique();
        $tituloSala  = $salasUnicas->count() === 1
            ? (string) $salasUnicas->first()
            : (string) ($metadatos['programa'] ?? 'OFICINA DE CÓMPUTO');

        $tituloSalaUpper = mb_strtoupper($tituloSala, 'UTF-8');
        $fechaHoy        = date('d/m/Y');

        if (array_key_exists('A3:L3', $sheet->getMergeCells())) {
            $sheet->unmergeCells('A3:L3');
        }

        // Anchos de columna equilibrados
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(42);

        // Construir tarjeta única con marco redondeado + escudo J.C. Tello + texto I.E.S.T.P. JULIO CÉSAR TELLO
        $textoEnImagen = $this->construirTarjetaInstituto($sheet);

        if (! $textoEnImagen) {
            $sheet->mergeCells('H2:H5');
            $sheet->setCellValue('H2', "I.E.S.T.P.\nJULIO CÉSAR\nTELLO");
            $sheet->getStyle('H2:H5')->applyFromArray([
                'font'      => ['name' => 'Times New Roman', 'size' => 10, 'bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ]);
        }

        // Recuadro gris superior derecho: INVENTARIO DE ...
        $sheet->mergeCells('J1:L5');
        $sheet->setCellValue('J1', 'INVENTARIO DE ' . $tituloSalaUpper);
        $sheet->getStyle('J1:L5')->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 14, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'],
            ],
            'borders'   => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '404040'],
                ],
            ],
        ]);

        // Fecha de entrega (G11) y Sala (J11:L11)
        $sheet->setCellValue('G11', $fechaHoy);
        $sheet->setCellValue('J11', $tituloSalaUpper);

        $nombreHoja = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', '', $tituloSalaUpper) ?: 'INVENTARIO';
        $sheet->setTitle(mb_substr($nombreHoja, 0, 31));

        // Rellenar filas de bienes clonando el estilo de la fila 14 y activando wrapText
        $estiloFilaBase = $sheet->getStyle('A14:L14');
        $filaActual     = 14;

        if ($listaBienes->isEmpty()) {
            $sheet->setCellValue('A14', 1);
            $sheet->getStyle('A14:L14')->getAlignment()->setWrapText(true);
            $filaActual = 15;
        } else {
            $item = 1;
            foreach ($listaBienes as $b) {
                if ($filaActual > 14) {
                    $sheet->duplicateStyle($estiloFilaBase, "A{$filaActual}:L{$filaActual}");
                }

                $desc  = (string) ($b->descripcion ?? '');
                $otras = (string) ($b->otras_especificaciones ?? '');
                $cbi   = (string) ($b->cbi ?? '');

                $lineas = max(
                    1,
                    (int) ceil(mb_strlen($desc) / 31),
                    (int) ceil(mb_strlen($otras) / 38)
                );
                $sheet->getRowDimension($filaActual)->setRowHeight(max(20, $lineas * 15));

                $sheet->setCellValue("A{$filaActual}", $item++);
                $sheet->setCellValue("B{$filaActual}", 'S/N');
                $sheet->setCellValue("C{$filaActual}", 'S/N');
                $sheet->setCellValue("D{$filaActual}", 'S/N');
                $sheet->setCellValue("E{$filaActual}", 'S/N');

                if (ctype_digit($cbi) && ! str_starts_with($cbi, '0')) {
                    $sheet->setCellValueExplicit("F{$filaActual}", (int) $cbi, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValueExplicit("F{$filaActual}", $cbi, DataType::TYPE_STRING);
                }

                $sheet->setCellValue("G{$filaActual}", $desc);
                $sheet->setCellValue("H{$filaActual}", (string) ($b->marca_nombre ?: 'S/M'));
                $sheet->setCellValue("I{$filaActual}", (string) ($b->modelo_nombre ?: 'S/M'));
                $sheet->setCellValueExplicit("J{$filaActual}", (string) ($b->serie_medida ?: 'S/SERIE'), DataType::TYPE_STRING);
                $sheet->setCellValue("K{$filaActual}", (int) ($b->estado_id ?? 2));
                $sheet->setCellValue("L{$filaActual}", $otras);

                $filaActual++;
            }

            $ultimaFilaDatos = $filaActual - 1;
            $sheet->getStyle("A14:L{$ultimaFilaDatos}")->getAlignment()->setWrapText(true);
        }

        // Pie: Leyenda de estados, Nota legal y bloque de 3 firmas
        $filaLeyenda = $filaActual + 1;
        $sheet->mergeCells("F{$filaLeyenda}:L{$filaLeyenda}");
        $sheet->setCellValue("F{$filaLeyenda}", 'ESTADO: 1= NUEVO, 2=BUENO, 3=REGULAR, 4= MALO   SIN MARCA=S/M, SIN NUMERO= S/N, SIN SERIE =S/SERIE');
        $sheet->getStyle("F{$filaLeyenda}")->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 9, 'bold' => false],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $filaNota        = $filaLeyenda + 2;
        $tieneImagenNota = false;
        foreach ($sheet->getDrawingCollection() as $drawing) {
            if ($drawing->getName() === 'Picture 2' || str_starts_with($drawing->getCoordinates(), 'F8')) {
                $drawing->setCoordinates("F{$filaNota}");
                if (method_exists($drawing, 'setCoordinates2')) {
                    $drawing->setCoordinates2('L' . ($filaNota + 2));
                }
                $tieneImagenNota = true;
            }
        }

        if (! $tieneImagenNota) {
            $sheet->mergeCells("F{$filaNota}:L" . ($filaNota + 2));
            $sheet->setCellValue("F{$filaNota}", 'NOTA : En caso de robo, extravío o deterioro, el servidor deberá dar cuenta a su jefe adjuntando la correspondiente denuncia policial del hecho, caso contrario asumirá la responsabilidad de reparación y/o reposición del bien.');
            $sheet->getStyle("F{$filaNota}:L" . ($filaNota + 2))->applyFromArray([
                'font'      => ['name' => 'Arial', 'size' => 10, 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
        }

        $filaFirmasLaterales = $filaNota + 5;
        $sheet->mergeCells("B{$filaFirmasLaterales}:F" . ($filaFirmasLaterales + 2));
        $sheet->setCellValue("B{$filaFirmasLaterales}", "----------------------------------------------\nGutierrez Chura Luis Alberto\nAsistente de Computación e informática\nTurno Nocturno");
        $sheet->getStyle("B{$filaFirmasLaterales}:F" . ($filaFirmasLaterales + 2))->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        $sheet->mergeCells("J{$filaFirmasLaterales}:L" . ($filaFirmasLaterales + 2));
        $sheet->setCellValue("J{$filaFirmasLaterales}", "-----------------------------------------------\nSegundo Teodulo Sanchez Rodriguez\nAsistente de Computación e informática\nTurno Diurno");
        $sheet->getStyle("J{$filaFirmasLaterales}:L" . ($filaFirmasLaterales + 2))->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        $filaFirmaCentral = $filaFirmasLaterales + 3;
        $sheet->mergeCells("G{$filaFirmaCentral}:I" . ($filaFirmaCentral + 2));
        $sheet->setCellValue("G{$filaFirmaCentral}", "-------------------------------------------\nMg. Miguel Mauro Culi Janampa\nCoordinador del Area Académica Computación e Informática");
        $sheet->getStyle("G{$filaFirmaCentral}:I" . ($filaFirmaCentral + 2))->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->setShowGridLines(true);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Genera una única imagen PNG con el marco redondeado, el escudo del I.E.S.T.P. Julio César Tello
     * y el texto institucional centrado dentro del marco, evitando que se superpongan o desalineen.
     */
    private function construirTarjetaInstituto($sheet): bool
    {
        if (! function_exists('imagecreatetruecolor')) {
            return false;
        }

        $drawings    = $sheet->getDrawingCollection();
        $logoBin     = null;
        $indicesQuitar = [];

        foreach ($drawings as $idx => $drawing) {
            if (! ($drawing instanceof Drawing)) {
                continue;
            }
            $nombre = $drawing->getName();
            $coord  = $drawing->getCoordinates();

            if ($nombre === '13 Imagen' || $coord === 'G2') {
                $data = @file_get_contents($drawing->getPath());
                if ($data !== false) {
                    $logoBin = $data;
                }
                $indicesQuitar[] = $idx;
            } elseif ($nombre === '7 Imagen' || $coord === 'F1') {
                $indicesQuitar[] = $idx;
            }
        }

        rsort($indicesQuitar);
        foreach ($indicesQuitar as $idx) {
            unset($drawings[$idx]);
        }

        $w   = 600;
        $h   = 172;
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        $transparente = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $transparente);
        imagealphablending($img, true);

        $bordeColor = imagecolorallocate($img, 38, 38, 38);
        $blanco     = imagecolorallocate($img, 255, 255, 255);
        $negro      = imagecolorallocate($img, 0, 0, 0);

        // Marco exterior redondeado (#262626) y relleno blanco interior
        $this->dibujarRectanguloRedondeado($img, 0, 0, $w - 1, $h - 1, 28, $bordeColor);
        $this->dibujarRectanguloRedondeado($img, 4, 4, $w - 5, $h - 5, 24, $blanco);

        // Pegar escudo J.C. Tello a la izquierda dentro del recuadro
        if ($logoBin !== null) {
            $logoSrc = @imagecreatefromstring($logoBin);
            if ($logoSrc) {
                $logoSize = 132;
                imagecopyresampled(
                    $img,
                    $logoSrc,
                    26,
                    (int) (($h - $logoSize) / 2),
                    0,
                    0,
                    $logoSize,
                    $logoSize,
                    imagesx($logoSrc),
                    imagesy($logoSrc)
                );
                imagedestroy($logoSrc);
            }
        }

        // Buscar fuente TrueType en Windows o Linux para escribir el nombre dentro de la tarjeta
        $fuentesPosibles = [
            'C:\\Windows\\Fonts\\timesbd.ttf',
            'C:\\Windows\\Fonts\\arialbd.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
        ];

        $fuente = null;
        foreach ($fuentesPosibles as $rutaFuente) {
            if (file_exists($rutaFuente)) {
                $fuente = $rutaFuente;
                break;
            }
        }

        $textoDibujado = false;
        if ($fuente !== null && function_exists('imagettftext')) {
            $lineas   = ['I.E.S.T.P.', 'JULIO CÉSAR', 'TELLO'];
            $fontSize = 19;
            $gap      = 12;
            $alturas  = [];
            $anchos   = [];

            foreach ($lineas as $linea) {
                $box       = imagettfbbox($fontSize, 0, $fuente, $linea);
                $anchos[]  = abs($box[2] - $box[0]);
                $alturas[] = abs($box[7] - $box[1]);
            }

            $altoTotal = array_sum($alturas) + $gap * (count($lineas) - 1);
            $y         = (int) (($h - $altoTotal) / 2) + $alturas[0];
            $centroX   = 385;

            foreach ($lineas as $i => $linea) {
                $x = (int) ($centroX - ($anchos[$i] / 2));
                imagettftext($img, $fontSize, 0, $x, $y, $negro, $fuente, $linea);
                if (isset($alturas[$i + 1])) {
                    $y += $alturas[$i + 1] + $gap;
                }
            }
            $textoDibujado = true;
        }

        $mem = new MemoryDrawing();
        $mem->setName('TarjetaInstituto');
        $mem->setImageResource($img);
        $mem->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
        $mem->setMimeType(MemoryDrawing::MIMETYPE_PNG);
        $mem->setCoordinates('G1');
        $mem->setOffsetX(22);
        $mem->setOffsetY(6);
        $mem->setWidth(300);
        $mem->setHeight(86);

        $drawings[] = $mem;

        return $textoDibujado;
    }

    private function dibujarRectanguloRedondeado($img, int $x1, int $y1, int $x2, int $y2, int $radio, int $color): void
    {
        imagefilledrectangle($img, $x1 + $radio, $y1, $x2 - $radio, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $radio, $x2, $y2 - $radio, $color);

        $diametro = $radio * 2;
        imagefilledellipse($img, $x1 + $radio, $y1 + $radio, $diametro, $diametro, $color);
        imagefilledellipse($img, $x2 - $radio, $y1 + $radio, $diametro, $diametro, $color);
        imagefilledellipse($img, $x1 + $radio, $y2 - $radio, $diametro, $diametro, $color);
        imagefilledellipse($img, $x2 - $radio, $y2 - $radio, $diametro, $diametro, $color);
    }

    /**
     * Construye desde cero el formato oficial de inventario (mismas posiciones que la plantilla):
     * cabecera institucional (filas 1-5), datos del responsable (filas 7-11),
     * encabezados de tabla en la fila 13 y datos desde la fila 14.
     * Es el formato que reconoce la importación (etiqueta "Docente responsable", "Sala" y 12 columnas).
     */
    private function crearFormatoBase(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('INVENTARIO');

        $anchos = ['A' => 6, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 16, 'F' => 13,
                   'G' => 35, 'H' => 16, 'I' => 16, 'J' => 25, 'K' => 9, 'L' => 42];
        foreach ($anchos as $col => $ancho) {
            $sheet->getColumnDimension($col)->setWidth($ancho);
        }
        foreach (range(1, 5) as $f) {
            $sheet->getRowDimension($f)->setRowHeight(20);
        }

        $etiqueta = [
            'font'      => ['name' => 'Times New Roman', 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $caja = [
            'font'      => ['name' => 'Times New Roman', 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]],
        ];

        // Fila 7: docente responsable / cargo
        $sheet->mergeCells('A7:C7');
        $sheet->setCellValue('A7', 'Docente responsable');
        $sheet->mergeCells('D7:G7');
        $sheet->setCellValue('D7', 'Mg. Miguel Mauro Culi Janampa');
        $sheet->setCellValue('I7', 'Cargo');
        $sheet->mergeCells('J7:L7');
        $sheet->setCellValue('J7', 'Coordinador del Area Académica Computación e Informática');

        // Fila 9: responsable de entrega / cargo
        $sheet->mergeCells('A9:C9');
        $sheet->setCellValue('A9', 'Responsable de entrega');
        $sheet->mergeCells('D9:G9');
        $sheet->setCellValue('D9', 'Segundo Teodulo Sanchez Rodriguez / Luis Alberto Gutierrez Chura');
        $sheet->setCellValue('I9', 'Cargo');
        $sheet->mergeCells('J9:L9');
        $sheet->setCellValue('J9', 'Asistente del area de Computación e Informática - T1 - T2');

        // Fila 11: fecha de entrega (G11) / sala (J11:L11)
        $sheet->mergeCells('A11:F11');
        $sheet->setCellValue('A11', 'Fecha de entrega');
        $sheet->setCellValue('I11', 'Sala');
        $sheet->mergeCells('J11:L11');

        foreach (['A7', 'A9', 'A11', 'I7', 'I9', 'I11'] as $celda) {
            $sheet->getStyle($celda)->applyFromArray($etiqueta);
        }
        foreach (['D7:G7', 'D9:G9', 'G11', 'J7:L7', 'J9:L9', 'J11:L11'] as $rango) {
            $sheet->getStyle($rango)->applyFromArray($caja);
        }
        foreach ([7, 9, 11] as $f) {
            $sheet->getRowDimension($f)->setRowHeight(24);
        }

        // Fila 13: encabezados oficiales (12 columnas)
        $encabezados = [
            'A' => ['N°', 'D9D9D9'],
            'B' => ['INV 2025', 'D9D9D9'],
            'C' => ['INV 2024', 'D9D9D9'],
            'D' => ['INV 2023', 'D9D9D9'],
            'E' => ['CODIGO PATRIMONIAL', 'D9D9D9'],
            'F' => ['CBI', 'F8CBAD'],
            'G' => ['DESCRIPCIÓN DEL BIEN', 'FFF2CC'],
            'H' => ['MARCA', 'C6E0B4'],
            'I' => ['MODELO', 'DDEBF7'],
            'J' => ['SERIE/MEDIDA', '9DC3E6'],
            'K' => ['ESTADO', 'FFFFFF'],
            'L' => ['OTRAS ESPECIFICACIONES', 'E2EFDA'],
        ];
        foreach ($encabezados as $col => [$texto, $color]) {
            $sheet->setCellValue("{$col}13", $texto);
            $sheet->getStyle("{$col}13")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
        }
        $sheet->getStyle('A13:L13')->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension(13)->setRowHeight(32);

        // Fila 14: estilo base que se clona para cada bien
        $sheet->getStyle('A14:L14')->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getStyle('G14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('L14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension(14)->setRowHeight(20);

        return $spreadsheet;
    }

    /**
     * Lee el Excel en el servidor con las mismas reglas que la validación del navegador
     * (se usa cuando el navegador no pudo leerlo, p. ej. sin acceso al CDN de SheetJS).
     */
    private function parsearExcelServidor(UploadedFile $archivo): array
    {
        try {
            $ruta   = $archivo->getRealPath();
            $reader = IOFactory::createReaderForFile($ruta);
            $reader->setReadDataOnly(true);
            $libro  = $reader->load($ruta);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'archivo' => 'No se pudo leer el archivo Excel seleccionado.',
            ]);
        }

        $norm = fn ($v) => mb_strtoupper(trim((string) preg_replace('/\s+/u', ' ', $this->textoCelda($v))), 'UTF-8');

        $colsValidas = [
            0  => ['N°', 'Nº'],
            1  => ['INV 2025', '2025'],
            2  => ['INV 2024', '2024'],
            3  => ['INV 2023', '2023'],
            4  => ['CODIGO PATRIMONIAL', 'COD. PATRI.'],
            5  => ['CBI'],
            6  => ['DESCRIPCIÓN DEL BIEN', 'DESCRIPCION DEL BIEN'],
            7  => ['MARCA'],
            8  => ['MODELO'],
            9  => ['SERIE/MEDIDA', 'SERIE / MEDIDA'],
            10 => ['ESTADO'],
            11 => ['OTRAS ESPECIFICACIONES'],
        ];

        $registros = [];
        $errores   = [];

        foreach ($libro->getWorksheetIterator() as $hoja) {
            $nombreHoja = $hoja->getTitle();
            $rows       = $hoja->toArray('', true, false, false);

            $filaHeader = -1;
            $limite     = min(16, count($rows) - 1);
            for ($r = 6; $r <= $limite; $r++) {
                $row = $rows[$r] ?? [];
                $ok  = true;
                for ($c = 0; $c <= 11; $c++) {
                    if (! in_array($norm($row[$c] ?? ''), $colsValidas[$c], true)) {
                        $ok = false;
                        break;
                    }
                }
                if ($ok) {
                    $filaHeader = $r;
                    break;
                }
            }

            if ($filaHeader === -1) {
                $errores[] = "Hoja \"{$nombreHoja}\": no coincide con las 12 columnas oficiales (N° a OTRAS ESPECIFICACIONES).";
                continue;
            }

            $tieneDocente = false;
            $salaNombre   = '';
            for ($r = 0; $r < $filaHeader; $r++) {
                $row = $rows[$r] ?? [];
                for ($c = 0; $c <= 11; $c++) {
                    $txt = $norm($row[$c] ?? '');
                    if ($txt === 'DOCENTE RESPONSABLE') {
                        $tieneDocente = true;
                    }
                    if ($txt === 'SALA') {
                        for ($k = $c + 1; $k <= 11; $k++) {
                            if ($norm($row[$k] ?? '') !== '') {
                                $salaNombre = $this->textoCelda($row[$k]);
                                break;
                            }
                        }
                    }
                }
            }

            if (! $tieneDocente) {
                $errores[] = "Hoja \"{$nombreHoja}\": falta la etiqueta institucional \"DOCENTE RESPONSABLE\".";
                continue;
            }

            if ($salaNombre === '') {
                $salaNombre = trim($nombreHoja);
            }

            for ($r = $filaHeader + 1; $r < count($rows); $r++) {
                $row  = $rows[$r] ?? [];
                $colA = $norm($row[0] ?? '');
                $cbi  = $norm($row[5] ?? '');
                $desc = $this->textoCelda($row[6] ?? '');

                if (str_starts_with($colA, 'ESTADO') || str_starts_with($colA, 'NOTA') || str_starts_with($norm($desc), 'ESTADO:')) {
                    break;
                }
                if ($cbi === '' || in_array($cbi, ['S/N', 'S/C', '-'], true)) {
                    continue;
                }

                $registros[] = [
                    'hoja'        => $nombreHoja,
                    'fila'        => $r + 1,
                    'sala'        => $salaNombre,
                    'cbi'         => $cbi,
                    'descripcion' => $desc,
                    'marca'       => $this->textoCelda($row[7] ?? ''),
                    'modelo'      => $this->textoCelda($row[8] ?? ''),
                    'serie'       => $this->textoCelda($row[9] ?? ''),
                    'estado'      => $this->textoCelda($row[10] ?? ''),
                    'otras'       => $this->textoCelda($row[11] ?? ''),
                ];
            }
        }

        if (! empty($errores)) {
            throw ValidationException::withMessages(['archivo' => $errores]);
        }

        return $registros;
    }

    /** Convierte el valor de una celda a texto (los números enteros no llevan ".0"). */
    private function textoCelda(mixed $valor): string
    {
        if (is_float($valor) && floor($valor) === $valor && abs($valor) < 1e15) {
            return (string) (int) $valor;
        }

        return trim((string) ($valor ?? ''));
    }

    private function parsearCsvNativo(UploadedFile $archivo): array
    {
        $handle = fopen($archivo->getRealPath(), 'r');
        if (! $handle) {
            throw ValidationException::withMessages(['archivo' => 'No se pudo leer el archivo subido.']);
        }

        $registros = [];
        $numFila   = 0;

        while (($cols = fgetcsv($handle, 0, ',')) !== false) {
            $numFila++;
            if (count($cols) === 1 && str_contains((string) $cols[0], ';')) {
                $cols = str_getcsv((string) $cols[0], ';');
            }
            if ($numFila <= 13 || count($cols) < 7) {
                continue;
            }
            $registros[] = [
                'hoja'        => 'CSV',
                'fila'        => $numFila,
                'sala'        => 'SALA GENERAL',
                'cbi'         => $cols[5] ?? '',
                'descripcion' => $cols[6] ?? '',
                'marca'       => $cols[7] ?? '',
                'modelo'      => $cols[8] ?? '',
                'serie'       => $cols[9] ?? '',
                'estado'      => $cols[10] ?? '',
                'otras'       => $cols[11] ?? '',
            ];
        }
        fclose($handle);

        return $registros;
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