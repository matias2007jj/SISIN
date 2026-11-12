@extends('plantilla.plantilla')

@section('title', 'SISIN - Toma de Inventario')
@section('titulo', 'Toma de Inventario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
@endpush

@section('contenido')
@php $global = auth()->user()->esSuperusuario() && ! auth()->user()->institucionActiva(); @endphp

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por programa, sala o número..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">Total Registros: <span id="contadorRegistros">{{ count($inventarios) }}</span></span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                @if ($global)<th>Institución</th>@endif
                <th>Programa de Estudio</th>
                <th>Serie - N°</th>
                <th>Sala</th>
                <th>Fecha entrega</th>
                <th class="text-center">Bienes</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inventarios as $inv)
                <tr class="fila-link" data-href="{{ route('inventarios.show', $inv) }}">
                    @if ($global)<td class="text-fecha">{{ $inv->programaEstudio->institucion->nombre ?? '-' }}</td>@endif
                    <td>{{ $inv->programaEstudio->nombre ?? '-' }}</td>
                    <td class="text-fecha">{{ $inv->serie }}-{{ str_pad($inv->numero, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $inv->nombre_sala }}</td>
                    <td class="text-fecha">{{ $inv->fecha_entrega?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center"><span class="badge-programa">{{ $inv->detalles_count }}</span></td>
                    <td class="text-center">{{ $inv->estado_inventario }}</td>
                    <td class="text-center">
                        <a href="{{ route('inventarios.show', $inv) }}" class="btn btn-outline-warning btn-action-icon">Ver</a>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $global ? 8 : 7 }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay inventarios registrados</h6>
                        <p class="text-muted fs-7 mb-0">Esta institución todavía no tiene tomas de inventario.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/organizacion.js') }}"></script>
@endpush
