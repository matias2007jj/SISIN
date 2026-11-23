@php
    $logoDrelmBase64 = '';
    $logoJctBase64   = '';
    $rutaXlsx        = storage_path('app/plantillas/formato_inventario.xlsx');

    if (file_exists($rutaXlsx) && class_exists(\ZipArchive::class)) {
        $zip = new \ZipArchive();
        if ($zip->open($rutaXlsx) === true) {
            $drelmBin = $zip->getFromName('xl/media/514d07f5abc6d2200c69b30a2f1cf3f4.gif');
            $jctBin   = $zip->getFromName('xl/media/f885cfaae31043b10884d768bb20ea8c.jpeg');
            if ($drelmBin !== false) $logoDrelmBase64 = 'data:image/gif;base64,' . base64_encode($drelmBin);
            if ($jctBin !== false)   $logoJctBase64   = 'data:image/jpeg;base64,' . base64_encode($jctBin);
            $zip->close();
        }
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Bienes - {{ $tituloSala }}</title>
    <link rel="stylesheet" href="{{ asset('css/bien-pdf.css') }}">
</head>
<body>

    <div class="barra-acciones no-print">
        <button type="button" class="btn-pdf" onclick="window.print()">Guardar como PDF / Imprimir</button>
        <button type="button" class="btn-cerrar" onclick="window.close()">Cerrar</button>
    </div>

    <table class="cabecera-top">
        <tr>
            <td class="col-top-izq">
                @if ($logoDrelmBase64)
                    <img src="{{ $logoDrelmBase64 }}" alt="DRELM MINEDU" class="logo-drelm">
                @endif
            </td>

            <td class="col-top-centro">
                <div class="instituto-pill">
                    @if ($logoJctBase64)
                        <img src="{{ $logoJctBase64 }}" alt="IESTP Julio César Tello" class="logo-jct">
                    @endif
                    <div class="instituto-texto">
                        I.E.S.T.P.<br>
                        JULIO CÉSAR<br>
                        TELLO
                    </div>
                </div>
            </td>

            <td class="col-top-der">
                <div class="titulo-gris">
                    INVENTARIO DE {{ mb_strtoupper($tituloSala, 'UTF-8') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="meta-col-lbl-izq">Docente responsable</td>
            <td colspan="2" class="meta-box">Mg. Miguel Mauro Culi Janampa</td>
            <td class="meta-col-lbl-der">Cargo</td>
            <td class="meta-col-val-der meta-box">Coordinador del Area Académica Computación e Informática</td>
        </tr>
        <tr class="meta-sep"><td colspan="5"></td></tr>
        <tr>
            <td class="meta-col-lbl-izq">Responsable de entrega</td>
            <td colspan="2" class="meta-box">Segundo Teodulo Sanchez Rodriguez / Luis Alberto Gutierrez Chura</td>
            <td class="meta-col-lbl-der">Cargo</td>
            <td class="meta-col-val-der meta-box">Asistente del area de Computación e Informática - T1 - T2</td>
        </tr>
        <tr class="meta-sep"><td colspan="5"></td></tr>
        <tr>
            <td class="meta-col-lbl-izq">Fecha de entrega</td>
            <td class="meta-col-val-1 meta-box">{{ $fechaHoy }}</td>
            <td class="meta-col-val-2"></td>
            <td class="meta-col-lbl-der">Sala</td>
            <td class="meta-col-val-der meta-box">{{ mb_strtoupper($tituloSala, 'UTF-8') }}</td>
        </tr>
    </table>

    <table class="tabla-inventario">
        <thead>
            <tr>
                <th class="th-gris col-nro">Nº</th>
                <th class="th-gris col-inv">INV 2025</th>
                <th class="th-gris col-inv">INV 2024</th>
                <th class="th-gris col-inv">INV 2023</th>
                <th class="th-gris col-pat">CODIGO PATRIMONIAL</th>
                <th class="th-naranja col-cbi">CBI</th>
                <th class="th-crema col-desc">DESCRIPCIÓN DEL BIEN</th>
                <th class="th-verde col-marca">MARCA</th>
                <th class="th-celeste col-modelo">MODELO</th>
                <th class="th-azul col-serie">SERIE/MEDIDA</th>
                <th class="th-estado col-estado">ESTADO</th>
                <th class="th-oliva col-otras">OTRAS ESPECIFICACIONES</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bienes as $idx => $b)
                <tr>
                    <td class="tc">{{ $idx + 1 }}</td>
                    <td class="tc">S/N</td>
                    <td class="tc">S/N</td>
                    <td class="tc">S/N</td>
                    <td class="tc">S/N</td>
                    <td class="tc">{{ $b->cbi }}</td>
                    <td class="tl">{{ $b->descripcion }}</td>
                    <td class="tc">{{ $b->marca_nombre ?: 'S/M' }}</td>
                    <td class="tc">{{ $b->modelo_nombre ?: 'S/M' }}</td>
                    <td class="tc">{{ $b->serie_medida ?: 'S/SERIE' }}</td>
                    <td class="tc">{{ $b->estado_id ?? 2 }}</td>
                    <td class="tl">{{ $b->otras_especificaciones ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="tc celda-vacia">No existen bienes registrados para el filtro seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="leyenda">
        ESTADO: 1= NUEVO, 2=BUENO, 3=REGULAR, 4= MALO &nbsp;&nbsp; SIN MARCA=S/M, SIN NUMERO= S/N, SIN SERIE =S/SERIE
    </div>

    <div class="nota-legal">
        NOTA : En caso de robo, extravío o deterioro, el servidor deberá dar cuenta a su jefe adjuntando la correspondiente denuncia policial del hecho, caso contrario asumirá la responsabilidad de reparación y/o reposición del bien.
    </div>

    <table class="firmas">
        <tr>
            <td class="firma-lateral">
                <div class="linea-firma">
                    Gutierrez Chura Luis Alberto<br>
                    Asistente de Computación e informática<br>
                    Turno Nocturno
                </div>
            </td>
            <td class="firma-central">
                <div class="linea-firma">
                    Mg. Miguel Mauro Culi Janampa<br>
                    Coordinador del Area Académica Computación e Informática
                </div>
            </td>
            <td class="firma-lateral">
                <div class="linea-firma">
                    Segundo Teodulo Sanchez Rodriguez<br>
                    Asistente de Computación e informática<br>
                    Turno Diurno
                </div>
            </td>
        </tr>
    </table>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 250);
        });
    </script>
</body>
</html>