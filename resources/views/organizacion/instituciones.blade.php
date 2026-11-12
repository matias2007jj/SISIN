@extends('plantilla.plantilla')

@section('title', 'SISIN - Instituciones')
@section('titulo', 'Instituciones')
@section('boton', 'Nueva Institución')
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
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Escribe para filtrar instituciones..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorRegistros">{{ count($instituciones) }}</span>
        </span>
    </div>
</div>

<p class="text-muted fs-7 mb-2">Haz doble clic en una institución para ver su personal, programas y cargos.</p>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="width: 140px;">RUC</th>
                <th style="min-width: 260px;">Institución</th>
                <th style="width: 140px;">Tipo</th>
                <th style="min-width: 180px;">Dirección</th>
                <th class="text-center" style="width: 110px;">Programas</th>
                <th class="text-center" style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($instituciones as $institucion)
                @php
                    $registro = [
                        'ruc'       => $institucion->ruc,
                        'nombre'    => $institucion->nombre,
                        'tipo'      => $institucion->tipo,
                        'direccion' => $institucion->direccion,
                    ];
                @endphp
                <tr class="fila-link" data-href="{{ route('instituciones.show', $institucion) }}" title="Doble clic para ver el personal">
                    <td class="text-fecha">{{ $institucion->ruc }}</td>
                    <td><span class="brand-chip-title">{{ $institucion->nombre }}</span></td>
                    <td class="text-fecha">{{ ucfirst(strtolower($institucion->tipo)) }}</td>
                    <td class="text-fecha">{{ $institucion->direccion ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge-programa">{{ $institucion->programas_count }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon"
                                    data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                    data-url="{{ route('instituciones.update', $institucion) }}"
                                    data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                Editar
                            </button>

                            <form action="{{ route('instituciones.destroy', $institucion) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Desea eliminar la institución {{ addslashes($institucion->nombre) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action-icon">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="6" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay instituciones registradas</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Nueva Institución" para agregar la primera.</p>
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
                <form id="formRegistro" method="POST" action="{{ route('instituciones.store') }}"
                      data-url-nuevo="{{ route('instituciones.store') }}"
                      data-titulo-nuevo="Nueva Institución" data-titulo-editar="Editar Institución">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" disabled>

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2 class="js-titulo">Nueva Institución</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="ruc">RUC</label>
                            <input type="text" id="ruc" name="ruc" maxlength="11" inputmode="numeric" pattern="[0-9]{11}" placeholder="11 dígitos" required>
                        </div>
                        <div class="campo mb-3">
                            <label for="tipo">Tipo de Institución</label>
                            <select id="tipo" name="tipo" required>
                                @foreach (\App\Models\Institucion::TIPOS as $tipo)
                                    <option value="{{ $tipo }}">{{ ucfirst(strtolower($tipo)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="campo mb-3">
                            <label for="nombre">Nombre de la Institución</label>
                            <input type="text" id="nombre" name="nombre" maxlength="150" class="text-uppercase" placeholder="Ingrese el nombre" required>
                        </div>
                        <div class="campo mb-3">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" maxlength="150" placeholder="Opcional">
                        </div>

                        <div class="js-solo-nuevo">
                            <hr class="my-3 opacity-25">
                            <p class="text-muted fs-7 mb-2">Usuario con el que ingresará esta institución</p>
                            <div class="campo mb-3">
                                <label for="username">Usuario</label>
                                <input type="text" id="username" name="username" maxlength="15" autocomplete="off" placeholder="Vacío = usar el RUC">
                            </div>
                            <div class="campo mb-3">
                                <label for="password">Contraseña</label>
                                <input type="text" id="password" name="password" maxlength="60" autocomplete="new-password" placeholder="Vacío = generar una automática">
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
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/organizacion.js') }}"></script>
@endpush
