@extends('layouts.sisin')

@section('title', 'Menú Principal - SISIN')

@section('content')
@php
    $u = auth()->user();
    $activos = $bienesActivos ?? 0;
    $inactivos = $bienesInactivos ?? 0;
    $totalBienes = $activos + $inactivos;
    $porcentajeActivo = $totalBienes > 0 ? round(($activos / $totalBienes) * 100, 1) : 0;
@endphp

<main class="container pb-5">
    <div class="page-head mb-4">
        <h2>Panel de Control</h2>
        <p>
            {{ $u->rol_etiqueta }}
            · {{ $institucion->nombre ?? 'Todas las instituciones' }}
            @if ($programaActivo)
                · Programa: {{ $programaActivo->nombre }}
            @endif
        </p>
    </div>

    @if ($global)
        <div class="alert alert-secondary border-0 mb-4" style="background: rgba(255,255,255,.06); color: #cbd5d1;">
            Está viendo todas las instituciones. Para trabajar con los bienes, salas o usuarios de una institución,
            elíjala en el menú de usuario → <strong>Institución activa</strong>.
        </div>
    @endif

    <div class="summary mb-4" style="grid-template-columns: repeat({{ count($resumen) }}, 1fr);">
        @foreach ($resumen as $item)
            <div class="summary-item {{ ! empty($item['warn']) ? 'is-warn' : '' }}">
                <div>
                    <strong>{{ $item['value'] }}</strong>
                    <span>{{ $item['label'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráfico de Bienes Activos vs Inactivos --}}
    <section class="dashboard-chart-card">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div>
                <h3 class="dashboard-chart-title mb-1">Estado de Bienes Patrimoniales</h3>
                <p class="dashboard-chart-subtitle mb-0">Comparativa entre bienes operativos activos y bienes inactivos</p>
            </div>
            <span class="chart-total-badge">Total Bienes: {{ $totalBienes }}</span>
        </div>

        <div class="row g-4 align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="chart-canvas-wrapper">
                    <canvas id="graficoEstadoBienes"
                            data-activos="{{ $activos }}"
                            data-inactivos="{{ $inactivos }}">
                    </canvas>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-7">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="estado-stat-box estado-activo">
                            <span class="estado-dot"></span>
                            <div>
                                <span class="estado-label">Bienes Activos</span>
                                <strong class="estado-valor">{{ $activos }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="estado-stat-box estado-inactivo">
                            <span class="estado-dot"></span>
                            <div>
                                <span class="estado-label">Bienes Inactivos</span>
                                <strong class="estado-valor">{{ $inactivos }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="operatividad-box">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="operatividad-text">Índice de operatividad</span>
                                <strong class="text-white">{{ $porcentajeActivo }}%</strong>
                            </div>
                            <div class="progress operatividad-progress">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $porcentajeActivo }}%"
                                     aria-valuenow="{{ $porcentajeActivo }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/menu.js') }}"></script>
@endsection