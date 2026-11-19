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
</main>
@endsection