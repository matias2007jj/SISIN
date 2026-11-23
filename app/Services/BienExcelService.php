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
            $registros = $this->parsearCsvNativo($archivo);
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
        abort_unless(file_exists($rutaPlantilla), 500, 'No se encontró la plantilla oficial en storage/app/plantillas/formato_inventario.xlsx');

        $spreadsheet = IOFactory::load($rutaPlantilla);
        $sheet       = $spreadsheet->getActiveSheet();

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