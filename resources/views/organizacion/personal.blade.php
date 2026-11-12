@extends('plantilla.plantilla')

@section('title', 'SISIN - Personal')
@section('titulo', 'Personal')
@section('boton', 'Nuevo Personal')
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
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por nombre, DNI, cargo o programa..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorRegistros">{{ count($personal) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="min-width: 230px;">Apellidos y Nombres</th>
                <th style="width: 110px;">DNI</th>
                <th style="min-width: 190px;">Contacto</th>
                <th style="min-width: 280px;">Cargo / Programa de Estudio</th>
                <th class="text-center" style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($personal as $persona)
                @php
                    $registro = [
                        'apellidos'    => $persona->apellidos,
                        'nombres'      => $persona->nombres,
                        'dni'          => $persona->dni,
                        'celular'      => $persona->celular,
                        'email'        => $persona->email,
                        'direccion'    => $persona->direccion,
                        'asignaciones' => $persona->asignaciones
                            ->map(fn ($a) => [
                                'cargo'               => $a->cargo,
                                'programa_estudio_id' => $a->programa_estudio_id,
                            ])
                            ->values(),
                    ];
                @endphp
                <tr>
                    <td><span class="brand-chip-title">{{ $persona->nombre_completo }}</span></td>
                    <td class="text-fecha">{{ $persona->dni }}</td>
                    <td class="text-fecha">
                        {{ $persona->celular ?: '-' }}
                        @if ($persona->email)
                            <br>{{ $persona->email }}
                        @endif
                    </td>
                    <td>
                        <div class="lista-asignacion">
                            @forelse ($persona->asignaciones->filter(fn ($a) => $a->programaEstudio) as $a)
                                <div>
                                    <span class="badge-cargo cargo-{{ strtolower($a->cargo) }}">{{ $a->cargo }}</span>
                                    <span class="programa">{{ $a->programaEstudio->nombre }}</span>
                                </div>
                            @empty
                                <span class="text-muted fs-7">Sin asignaciones</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon"
                                    data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                    data-url="{{ route('personal.update', $persona) }}"
                                    data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                Editar
                            </button>

                            <form action="{{ route('personal.destroy', $persona) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Desea eliminar a {{ addslashes($persona->nombre_completo) }}?')">
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
                        <h6 class="text-white fw-bold mb-1">No hay personal registrado</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Nuevo Personal" para agregar el primero.</p>
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
                <form id="formRegistro" method="POST" action="{{ route('personal.store') }}"
                      data-url-nuevo="{{ route('personal.store') }}"
                      data-titulo-nuevo="Nuevo Personal" data-titulo-editar="Editar Personal">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" disabled>

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2 class="js-titulo">Nuevo Personal</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="campo">
                                    <label for="apellidos">Apellidos</label>
                                    <input type="text" id="apellidos" name="apellidos" maxlength="60" class="text-uppercase" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="campo">
                                    <label for="nombres">Nombres</label>
                                    <input type="text" id="nombres" name="nombres" maxlength="60" class="text-uppercase" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="dni">DNI</label>
                                    <input type="text" id="dni" name="dni" maxlength="8" inputmode="numeric" pattern="[0-9]{8}" placeholder="8 dígitos" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="celular">Celular</label>
                                    <input type="text" id="celular" name="celular" maxlength="30">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="email">Correo</label>
                                    <input type="email" id="email" name="email" maxlength="100">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="campo">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" id="direccion" name="direccion" maxlength="150">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="asignaciones-titulo">Cargos por Programa de Estudio</span>
                            <button type="button" class="btn btn-outline-success btn-sm" id="btnAgregarAsignacion">Agregar cargo</button>
                        </div>
                        <p class="asignaciones-vacio" id="asignacionesVacio">Sin cargos asignados. Use "Agregar cargo" para asignar coordinador, docente o asistente a un programa.</p>
                        <div id="listaAsignaciones"></div>
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

<template id="tplAsignacion">
    <div class="asignacion-row">
        <select name="asignaciones[__i__][cargo]" class="js-cargo" required>
            @foreach ($cargos as $cargo)
                <option value="{{ $cargo }}">{{ ucfirst(strtolower($cargo)) }}</option>
            @endforeach
        </select>
        <select name="asignaciones[__i__][programa_estudio_id]" class="js-programa" required>
            <option value="">Seleccione un programa...</option>
            @foreach ($programas as $programa)
                <option value="{{ $programa->id }}">{{ $programa->nombre }}{{ $programa->institucion ? ' - ' . \Illuminate\Support\Str::limit($programa->institucion->nombre, 35) : '' }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-outline-danger btn-sm js-quitar">Quitar</button>
    </div>
</template>
@endsection

@push('scripts')
<script src="{{ asset('js/organizacion.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lista = document.getElementById('listaAsignaciones');
        var plantilla = document.getElementById('tplAsignacion');
        var vacio = document.getElementById('asignacionesVacio');
        var contador = 0;

        function actualizarVacio() {
            vacio.style.display = lista.children.length ? 'none' : '';
        }

        function agregar(asignacion) {
            var envoltorio = document.createElement('div');
            envoltorio.innerHTML = plantilla.innerHTML.split('__i__').join(contador++).trim();
            var fila = envoltorio.firstElementChild;

            if (asignacion) {
                fila.querySelector('.js-cargo').value = asignacion.cargo;
                fila.querySelector('.js-programa').value = asignacion.programa_estudio_id;
            }

            lista.appendChild(fila);
            actualizarVacio();
        }

        document.getElementById('btnAgregarAsignacion').addEventListener('click', function () {
            agregar(null);
        });

        lista.addEventListener('click', function (ev) {
            var quitar = ev.target.closest('.js-quitar');
            if (!quitar) return;
            quitar.closest('.asignacion-row').remove();
            actualizarVacio();
        });

        document.addEventListener('registro:abrir', function (ev) {
            lista.innerHTML = '';
            ((ev.detail && ev.detail.asignaciones) || []).forEach(agregar);
            actualizarVacio();
        });
    });
</script>
@endpush
