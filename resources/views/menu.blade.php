@extends('layouts.sisin')

@section('title', 'Menú Principal - SISIN')

@section('content')
@php $u = auth()->user(); @endphp

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

    {{-- Resumen según el rol --}}
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

    {{-- Accesos rápidos: solo lo que este rol puede usar --}}
    <div class="row g-3">
        @foreach ($accesos as $acceso)
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ $acceso['url'] }}" class="text-decoration-none">
                    <div class="menu-card h-100 p-4">
                        <h5 class="card-title fw-bold mb-1">{{ $acceso['titulo'] }}</h5>
                        <p class="card-text mb-0">{{ $acceso['texto'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @if (empty($accesos))
        <p class="text-muted">Su usuario todavía no tiene accesos asignados. Pida a su administrador que le asigne un programa de estudio.</p>
    @endif
</main>
@endsection
