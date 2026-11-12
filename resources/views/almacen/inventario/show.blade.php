@extends('plantilla.plantilla')

@section('title', 'SISIN - Inventario ' . $inventario->serie . '-' . $inventario->numero)
@section('titulo', 'Inventario ' . $inventario->serie . '-' . str_pad($inventario->numero, 6, '0', STR_PAD_LEFT))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
@endpush

@section('contenido')
<div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
    <div class="text-muted fs-7">
        <div><strong class="text-white">Institución:</strong> {{ $inventario->programaEstudio->institucion->nombre ?? '-' }}</div>
        <div><strong class="text-white">Programa:</strong> {{ $inventario->programaEstudio->nombre ?? '-' }}</div>
        <div><strong class="text-white">Sala:</strong> {{ $inventario->nombre_sala }}</div>
        <div><strong class="text-white">Coordinador:</strong> {{ $inventario->nombre_coordinador ?: '-' }}</div>
        <div><strong class="text-white">Estado:</strong> {{ $inventario->estado_inventario }}</div>
    </div>
    <div>
        <a href="{{ route('inventarios.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th>CBI</th><th>Descripción</th><th>Grupo</th><th>Tipo</th>
                <th>Marca</th><th>Modelo</th><th>Serie / Medida</th><th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inventario->detalles as $d)
                <tr>
                    <td class="text-fecha">{{ $d->cbi }}</td>
                    <td>{{ $d->descripcion }}</td>
                    <td>{{ $d->nombre_grupo ?: '-' }}</td>
                    <td>{{ $d->nombre_tipo ?: '-' }}</td>
                    <td>{{ $d->nombre_marca ?: '-' }}</td>
                    <td>{{ $d->nombre_modelo ?: '-' }}</td>
                    <td>{{ $d->serie_medida ?: '-' }}</td>
                    <td>{{ $d->nombre_estado ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Este inventario no tiene bienes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
