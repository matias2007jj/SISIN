@extends('plantilla.plantilla')

@section('title', 'SISIN - Bienes')
@section('titulo', 'Bienes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
@endpush

@section('contenido')
@php $global = auth()->user()->esSuperusuario() && ! auth()->user()->institucionActiva(); @endphp

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por CBI, descripción, sala..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">Total Registros: <span id="contadorRegistros">{{ count($bienes) }}</span></span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                @if ($global)<th>Institución</th>@endif
                <th>CBI</th><th>Descripción</th><th>Sala</th><th>Grupo</th>
                <th>Tipo</th><th>Marca</th><th>Modelo</th><th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bienes as $b)
                <tr>
                    @if ($global)<td class="text-fecha">{{ $b->sala->programaEstudio->institucion->nombre ?? '-' }}</td>@endif
                    <td class="text-fecha">{{ $b->cbi }}</td>
                    <td>{{ $b->descripcion }}</td>
                    <td>{{ $b->sala->nombre ?? '-' }}</td>
                    <td>{{ $b->grupo->nombre ?? '-' }}</td>
                    <td>{{ $b->tipo->nombre ?? '-' }}</td>
                    <td>{{ $b->marca->nombre ?? '-' }}</td>
                    <td>{{ $b->modelo->nombre ?? '-' }}</td>
                    <td>{{ $b->estado->nombre ?? '-' }}</td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $global ? 9 : 8 }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay bienes registrados</h6>
                        <p class="text-muted fs-7 mb-0">Esta institución todavía no tiene bienes.</p>
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
