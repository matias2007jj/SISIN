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
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por RUC, nombre, tipo o usuario..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <small class="text-muted me-3">Doble clic en una fila para ingresar</small>
        <span class="badge-count">
            Total Registros: <span id="contadorRegistros">{{ count($instituciones) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="width: 120px;">RUC</th>
                <th style="min-width: 240px;">Institución</th>
                <th style="width: 130px;">Tipo</th>
                <th style="min-width: 180px;">Dirección</th>
                <th class="text-center" style="width: 110px;">Programas</th>
                <th style="min-width: 150px;">Usuario Admin</th>
                <th class="text-center" style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($instituciones as $inst)
                @php
                    $admin = $inst->usuarios->first();
                    $registro = [
                        'ruc'       => $inst->ruc,
                        'nombre'    => $inst->nombre,
                        'tipo'      => $inst->tipo,
                        'direccion' => $inst->direccion ?? '',
                        'username'  => $admin?->username ?? '',
                    ];
                @endphp
                <tr class="fila-link" data-href="{{ route('instituciones.show', $inst) }}" title="Doble clic para ingresar">
                    <td class="text-fecha">{{ $inst->ruc }}</td>
                    <td><span class="brand-chip-title">{{ $inst->nombre }}</span></td>
                    <td class="text-fecha">{{ ucfirst(strtolower($inst->tipo)) }}</td>
                    <td class="text-fecha">{{ $inst->direccion ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge-rol">{{ $inst->programas_count }}</span>
                    </td>
                    <td class="text-fecha">{{ $admin?->username ?? '-' }}</td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon"
                                    data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                    data-url="{{ route('instituciones.update', $inst) }}"
                                    data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                Editar
                            </button>

                            <form action="{{ route('instituciones.destroy', $inst) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar la institución {{ addslashes($inst->nombre) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action-icon">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="7" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay instituciones registradas</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Nueva Institución" para registrar la primera.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modales')
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <p class="asignaciones-titulo mb-2">1. Datos de la Institución</p>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="campo">
                                    <label for="ruc">RUC</label>
                                    <input type="text" id="ruc" name="ruc" maxlength="11" inputmode="numeric" pattern="[0-9]{11}" placeholder="11 dígitos" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="campo">
                                    <label for="tipo">Tipo de Institución</label>
                                    <select id="tipo" name="tipo" required>
                                        @foreach (\App\Models\Institucion::TIPOS as $t)
                                            <option value="{{ $t }}">{{ ucfirst(strtolower($t)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="campo">
                                    <label for="nombre">Nombre de la Institución</label>
                                    <input type="text" id="nombre" name="nombre" maxlength="150" class="text-uppercase" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="campo">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" id="direccion" name="direccion" maxlength="150">
                                </div>
                            </div>
                        </div>

                        <p class="asignaciones-titulo mb-2">2. Acceso del Administrador de Institución</p>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="campo">
                                    <label for="username">Usuario Administrador</label>
                                    <input type="text" id="username" name="username" maxlength="100" autocomplete="off" placeholder="Vacío = RUC">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="campo">
                                    <label for="password">Contraseña</label>
                                    <input type="text" id="password" name="password" maxlength="60" autocomplete="new-password" placeholder="Vacío = automática (al editar: no cambiar)">
                                </div>
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