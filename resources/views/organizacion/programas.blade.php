@extends('plantilla.plantilla')

@section('title', 'SISIN - Programas de Estudio')
@section('titulo', 'Programas de Estudio')
@section('boton', 'Nuevo Programa')
@section('modal_id', 'modalRegistro')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
@endpush

@section('contenido')
@include('organizacion._alertas')

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Escribe para filtrar programas..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorRegistros">{{ count($programas) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="min-width: 240px;">Programa de Estudio</th>
                <th style="min-width: 240px;">Institución</th>
                <th style="min-width: 180px;">Coordinador</th>
                <th class="text-center" style="width: 100px;">Estado</th>
                <th class="text-center" style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($programas as $programa)
                @php
                    $registro = [
                        'institucion_id' => $programa->institucion_id,
                        'nombre'         => $programa->nombre,
                        'abreviatura'    => $programa->abreviatura,
                        'observacion'    => $programa->observacion,
                        'activo'         => $programa->activo,
                    ];
                @endphp
                <tr>
                    <td>
                        <span class="brand-chip-title">{{ $programa->nombre }}</span>
                        @if ($programa->abreviatura)
                            <span class="text-fecha ms-1">({{ $programa->abreviatura }})</span>
                        @endif
                    </td>
                    <td class="text-fecha">{{ $programa->institucion?->nombre ?? '-' }}</td>
                    <td class="text-fecha">{{ $programa->coordinador?->nombre_completo ?? 'Sin asignar' }}</td>
                    <td class="text-center">
                        @if ($programa->activo)
                            <span class="badge-estado-activo">ACTIVO</span>
                        @else
                            <span class="badge-estado-inactivo">INACTIVO</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon"
                                    data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                    data-url="{{ route('programas.update', $programa) }}"
                                    data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                Editar
                            </button>

                            <form action="{{ route('programas.destroy', $programa) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Desea eliminar el programa {{ addslashes($programa->nombre) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action-icon">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="5" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay programas de estudio registrados</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Nuevo Programa" para agregar el primero.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modales')
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form id="formRegistro" method="POST" action="{{ route('programas.store') }}"
                      data-url-nuevo="{{ route('programas.store') }}"
                      data-titulo-nuevo="Nuevo Programa de Estudio" data-titulo-editar="Editar Programa de Estudio">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" disabled>

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2 class="js-titulo">Nuevo Programa de Estudio</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="institucion_id">Institución</label>
                            <select id="institucion_id" name="institucion_id" required>
                                <option value="">Seleccione una institución...</option>
                                @foreach ($instituciones as $institucion)
                                    <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-8">
                                <div class="campo">
                                    <label for="nombre">Nombre del Programa</label>
                                    <input type="text" id="nombre" name="nombre" maxlength="100" class="text-uppercase" placeholder="Ingrese el nombre" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="abreviatura">Abreviatura</label>
                                    <input type="text" id="abreviatura" name="abreviatura" maxlength="30" class="text-uppercase" placeholder="Ej. DSI">
                                </div>
                            </div>
                        </div>
                        <div class="campo mb-3">
                            <label for="observacion">Observación</label>
                            <input type="text" id="observacion" name="observacion" maxlength="150" placeholder="Opcional">
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" checked>
                            <label class="form-check-label" for="activo">Programa activo</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-guardar">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/organizacion.js') }}"></script>
@endpush
