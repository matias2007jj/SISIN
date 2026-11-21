@extends('plantilla.plantilla')

@section('title', 'SISIN - ' . $institucion->nombre)
@section('titulo', $institucion->nombre)
@if ($puedeGestionar)
    @section('boton', 'Agregar Programa de Estudio')
    @section('modal_id', 'modalRegistro')
@endif

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
@endpush

@section('contenido')
@include('organizacion._alertas')

<div class="ficha-institucion">
    <div class="datos">
        <div><strong>Tipo:</strong> {{ ucfirst(strtolower($institucion->tipo)) }}</div>
        <div><strong>RUC:</strong> {{ $institucion->ruc }}</div>
        <div><strong>Dirección:</strong> {{ $institucion->direccion ?: '-' }}</div>
    </div>

    @if (auth()->user()->esSuperusuario())
        <div class="d-inline-flex flex-wrap gap-2">
            <a href="{{ route('instituciones.index') }}" class="btn btn-outline-secondary btn-sm">Volver a Instituciones</a>
        </div>
    @endif
</div>

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por programa, abreviatura o coordinador..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Programas: <span id="contadorRegistros">{{ count($programas) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="min-width: 240px;">Programa de Estudio</th>
                <th style="min-width: 200px;">Coordinador</th>
                <th style="min-width: 180px;">Observación</th>
                <th class="text-center" style="width: 100px;">Estado</th>
                @if ($puedeGestionar)
                    <th class="text-center" style="width: 170px;">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($programas as $prog)
                @php
                    $registro = [
                        'nombre'      => $prog->nombre,
                        'abreviatura' => $prog->abreviatura,
                        'observacion' => $prog->observacion,
                        'activo'      => $prog->activo,
                    ];
                @endphp
                <tr>
                    <td>
                        <span class="brand-chip-title">{{ $prog->nombre }}</span>
                        @if ($prog->abreviatura)
                            <span class="text-fecha ms-1">({{ $prog->abreviatura }})</span>
                        @endif
                    </td>
                    <td class="text-fecha">{{ $prog->coordinador?->nombre_completo ?? 'Sin asignar' }}</td>
                    <td class="text-fecha">{{ $prog->observacion ?: '-' }}</td>
                    <td class="text-center">
                        @if ($prog->activo)
                            <span class="badge-estado-activo">ACTIVO</span>
                        @else
                            <span class="badge-estado-inactivo">INACTIVO</span>
                        @endif
                    </td>
                    @if ($puedeGestionar)
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-outline-warning btn-action-icon"
                                        data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                        data-url="{{ route('instituciones.programas.update', [$institucion, $prog]) }}"
                                        data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                    Editar
                                </button>

                                <form action="{{ route('instituciones.programas.destroy', [$institucion, $prog]) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar el programa {{ addslashes($prog->nombre) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action-icon">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $puedeGestionar ? 5 : 4 }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">Aún no hay programas de estudio registrados</h6>
                        @if ($puedeGestionar)
                            <p class="text-muted fs-7 mb-0">Haz clic en "Agregar Programa de Estudio" para registrar el primero.</p>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@if ($puedeGestionar)
@section('modales')
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form id="formRegistro" method="POST" action="{{ route('instituciones.programas.store', $institucion) }}"
                      data-url-nuevo="{{ route('instituciones.programas.store', $institucion) }}"
                      data-titulo-nuevo="Agregar Programa de Estudio" data-titulo-editar="Editar Programa de Estudio">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" disabled>

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2 class="js-titulo">Agregar Programa de Estudio</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-8">
                                <div class="campo">
                                    <label for="nombre">Nombre del Programa</label>
                                    <input type="text" id="nombre" name="nombre" maxlength="100" class="text-uppercase" placeholder="Ej. Desarrollo de Sistemas de Información" required>
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

                        <div class="js-solo-editar d-none form-check form-switch">
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
@endif

@push('scripts')
<script src="{{ asset('js/organizacion.js') }}"></script>
@endpush