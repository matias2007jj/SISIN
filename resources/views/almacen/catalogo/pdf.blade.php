<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }} - SISIN</title>
    <link rel="stylesheet" href="{{ asset('css/catalogo-pdf.css') }}">
</head>
<body>
    <div class="toolbar-print">
        <span>Vista previa de reporte PDF — {{ $titulo }}</span>
        <div class="toolbar-actions">
            <button type="button" class="btn-print" onclick="window.print()">Imprimir / Guardar como PDF</button>
            <button type="button" class="btn-close-tab" onclick="window.close()">Cerrar</button>
        </div>
    </div>

    <div class="hoja">
        <div class="header">
            <div class="header-left">
                <div class="sistema">SISIN · Sistema de Inventario</div>
                <h1>{{ $titulo }}</h1>
                <div class="institucion">{{ $institucion }}</div>
            </div>
            <div class="header-right">
                <div><strong>Fecha:</strong> {{ date('d/m/Y') }}</div>
                <div><strong>Hora:</strong> {{ date('H:i') }}</div>
                <span class="badge-total">Total: {{ count($registros) }} registros</span>
            </div>
        </div>

        @if (! empty($filtroTexto) || ! empty($filtroPrograma))
            <div class="filtros-bar">
                <strong>Filtros aplicados:</strong>
                @if (! empty($filtroPrograma))
                    Programa: <span>{{ $filtroPrograma }}</span>
                @endif
                @if (! empty($filtroTexto))
                    {{ ! empty($filtroPrograma) ? ' | ' : '' }}Búsqueda: <span>"{{ mb_strtoupper($filtroTexto, 'UTF-8') }}"</span>
                @endif
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th class="center col-num">N°</th>
                    <th>Nombre</th>
                    @if ($catalogo !== 'estado')
                        @if ($esSuper)
                            <th>Institución</th>
                        @endif
                        <th>Programa de Estudio</th>
                        @if ($catalogo === 'sala')
                            <th>Ubicación / Observación</th>
                        @endif
                    @endif
                    <th class="center col-fecha">Creación</th>
                    <th class="center col-fecha">Modificación</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registros as $idx => $item)
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="nombre-celda">{{ $item->nombre }}</td>
                        @if ($catalogo !== 'estado')
                            @if ($esSuper)
                                <td>{{ $item->institucion_nombre ?? 'Sin institución' }}</td>
                            @endif
                            <td>
                                <span class="pill-programa">{{ $item->programa_estudio_nombre ?? 'General / N/A' }}</span>
                            </td>
                            @if ($catalogo === 'sala')
                                <td>{{ $item->observacion ?: '-' }}</td>
                            @endif
                        @endif
                        <td class="center">{{ ! empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="center">{{ ! empty($item->updated_at) ? \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center empty-row">
                            No hay registros disponibles para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <span>Generado por: {{ auth()->user()->username ?? 'Sistema' }}</span>
            <span>SISIN · Reporte de Tablas Generales</span>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
</body>
</html>