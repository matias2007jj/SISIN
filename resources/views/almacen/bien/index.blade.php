@extends('plantilla.plantilla')

@section('title', 'SISIN - Bienes')
@section('titulo', 'Bienes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
@endpush

@section('contenido')
@php $global = auth()->user()->esSuperusuario() && ! auth()->user()->institucionActiva(); @endphp

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-5 col-lg-4">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por CBI, descripción, sala..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>
    <div class="col-12 col-md-7 text-md-end d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <span class="badge-count">Total Registros: <span id="contadorRegistros">{{ count($bienes) }}</span></span>
        @if (!$global)
            <button type="button" class="btn btn-success btn-action-icon" id="btnNuevoBien" style="background-color:#10b981; border:none;">
                + Nuevo Bien
            </button>
        @endif
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                @if ($global)
                    <th>Institución</th>
                    <th>Programa</th>
                @endif
                <th>CBI</th>
                <th>Descripción</th>
                <th>Sala</th>
                <th>Grupo</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Estado</th>
                @if (!$global)
                    <th class="text-center">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($bienes as $b)
                <tr class="{{ $b->estado_id == 6 ? 'opacity-50' : '' }}">
                    @if ($global)
                        <td class="text-fecha">{{ $b->institucion_nombre ?? '-' }}</td>
                        <td><span class="badge-programa">{{ $b->programa_estudio_nombre ?? '-' }}</span></td>
                    @endif
                    <td class="text-fecha fw-bold">{{ $b->cbi }}</td>
                    <td class="fw-semibold text-white">{{ $b->descripcion }}</td>
                    <td>{{ $b->sala_nombre ?? '-' }}</td>
                    <td>{{ $b->grupo_nombre ?? '-' }}</td>
                    <td>{{ $b->tipo_nombre ?? '-' }}</td>
                    <td>{{ $b->marca_nombre ?? '-' }}</td>
                    <td>{{ $b->modelo_nombre ?? '-' }}</td>
                    <td>
                        @if ($b->estado_id == 6)
                            <span class="badge bg-danger">{{ $b->estado_nombre }}</span>
                        @else
                            <span class="badge bg-success">{{ $b->estado_nombre }}</span>
                        @endif
                    </td>
                    @if (!$global)
                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-outline-warning btn-action-icon btn-editar"
                                data-bien='@json($b)'
                                data-url="{{ route('bienes.update', $b->id) }}">
                                Editar
                            </button>

                            @if ($b->estado_id != 6)
                                <form action="{{ route('bienes.destroy', $b->id) }}" method="POST" class="d-inline form-desactivar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action-icon">
                                        Inactivar
                                    </button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $global ? 10 : 9 }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay bienes registrados</h6>
                        <p class="text-muted fs-7 mb-0">Este programa de estudio todavía no tiene bienes.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (!$global)
<div class="modal fade" id="modalBien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formBien" method="POST" action="{{ route('bienes.store') }}" data-store-url="{{ route('bienes.store') }}" class="modal-content mi-modal">
            @csrf
            <input type="hidden" name="_method" id="metodoForm" value="POST">

            <div class="modal-header">
                <div class="titulo">
                    <span class="punto"></span>
                    <h2 id="tituloModalBien">Nuevo Bien</h2>
                </div>
                <button type="button" class="cerrar" data-bs-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4 campo">
                        <label for="cbi">CBI *</label>
                        <input type="text" name="cbi" id="cbi" maxlength="10" class="text-uppercase" placeholder="EJ. 000123" required>
                    </div>
                    <div class="col-md-8 campo">
                        <label for="descripcion">Descripción *</label>
                        <input type="text" name="descripcion" id="descripcion" maxlength="100" class="text-uppercase" placeholder="INGRESE LA DESCRIPCIÓN DEL BIEN" required>
                    </div>

                    <div class="col-md-6 campo">
                        <label for="sala_id">Sala *</label>
                        <select name="sala_id" id="sala_id" required>
                            <option value="">Seleccione una sala...</option>
                            @foreach ($salas as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 campo">
                        <label for="grupo_id">Grupo *</label>
                        <select name="grupo_id" id="grupo_id" required>
                            <option value="">Seleccione un grupo...</option>
                            @foreach ($grupos as $g)
                                <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="tipo_id">Tipo *</label>
                        <select name="tipo_id" id="tipo_id" required>
                            <option value="">Seleccione un tipo...</option>
                            @foreach ($tipos as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="marca_id">Marca</label>
                        <select name="marca_id" id="marca_id">
                            <option value="">Sin marca</option>
                            @foreach ($marcas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="modelo_id">Modelo</label>
                        <select name="modelo_id" id="modelo_id">
                            <option value="">Sin modelo</option>
                            @foreach ($modelos as $mo)
                                <option value="{{ $mo->id }}">{{ $mo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="estado_id">Estado *</label>
                        <select name="estado_id" id="estado_id" required>
                            @foreach ($estados as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8 campo">
                        <label for="serie_medida">Serie / Medida</label>
                        <input type="text" name="serie_medida" id="serie_medida" maxlength="100" class="text-uppercase" placeholder="NRO. DE SERIE O MEDIDAS">
                    </div>

                    <div class="col-12 campo">
                        <label for="otras_especificaciones">Otras Especificaciones</label>
                        <input type="text" name="otras_especificaciones" id="otras_especificaciones" maxlength="100" class="text-uppercase" placeholder="OBSERVACIONES O DETALLES ADICIONALES">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-guardar">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/organizacion.js') }}"></script>
    <script src="{{ asset('js/bien.js') }}"></script>
@endpush