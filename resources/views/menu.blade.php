@extends('layouts.sisin')

@section('title', 'Menú Principal - SISIN')

@section('content')
@php
    // Resumen: pásalo desde el controlador con view('menu', compact('stats')).
    // Si no lo pasas, se muestran ceros.
    $stats = $stats ?? [];
    $summary = [
        ['label' => 'Productos',        'value' => $stats['productos'] ?? 0,  'icon' => 'fa-cart-shopping'],
        ['label' => 'Clientes',         'value' => $stats['clientes'] ?? 0,   'icon' => 'fa-user-group'],
        ['label' => 'Facturas del mes', 'value' => $stats['facturas'] ?? 0,   'icon' => 'fa-receipt'],
        ['label' => 'Con stock bajo',   'value' => $stats['stock_bajo'] ?? 0, 'icon' => 'fa-triangle-exclamation', 'warn' => true],
    ];
@endphp

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container py-3">
        <div class="page-head mb-4">
            <h2>Panel de Control</h2>
            <p>Resumen del inventario y accesos a los módulos del sistema.</p>
        </div>

        <!-- Resumen -->
        <section class="summary mb-4" aria-label="Resumen">
            @foreach ($summary as $s)
                <div class="summary-item {{ !empty($s['warn']) && $s['value'] > 0 ? 'is-warn' : '' }}">
                    <i class="fa-solid {{ $s['icon'] }}"></i>
                    <div>
                        <strong>{{ number_format($s['value']) }}</strong>
                        <span>{{ $s['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Módulos -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card menu-card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                        <span class="module-icon"><i class="fa-solid fa-database"></i></span>
                        <h5 class="card-title fw-semibold mb-1">Módulo 1</h5>
                        <p class="card-text mb-4 flex-grow-1">Gestión y registro de datos.</p>
                        <a href="#" class="btn btn-green w-100">Ingresar</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card menu-card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                        <span class="module-icon"><i class="fa-solid fa-chart-column"></i></span>
                        <h5 class="card-title fw-semibold mb-1">Reportes</h5>
                        <p class="card-text mb-4 flex-grow-1">Consulta y exportación de reportes.</p>
                        <a href="#" class="btn btn-green w-100">Ver reportes</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card menu-card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                        <span class="module-icon"><i class="fa-solid fa-gear"></i></span>
                        <h5 class="card-title fw-semibold mb-1">Configuración</h5>
                        <p class="card-text mb-4 flex-grow-1">Ajustes generales del sistema.</p>
                        <a href="#" class="btn btn-secondary-custom w-100">Configurar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection