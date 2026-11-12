@extends('plantilla.plantilla')

@section('title', 'SISIN - ' . $institucion->nombre)
@section('titulo', $institucion->nombre)
@section('boton', 'Agregar Personal')
@section('modal_id', 'modalRegistro')

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

    <div class="d-inline-flex flex-wrap gap-2">
        <a href="{{ route('instituciones.index') }}" class="btn btn-outline-secondary btn-sm">Volver a Instituciones</a>
        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPrograma">Agregar Programa de Estudio</button>
    </div>
</div>

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por nombre, DNI, programa o cargo..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorRegistros">{{ count($asignaciones) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="min-width: 220px;">Apellidos y Nombres</th>
                <th style="width: 110px;">DNI</th>
                <th style="min-width: 220px;">Programa de Estudio</th>
                <th style="width: 140px;">Rol</th>
                <th style="min-width: 170px;">Contacto</th>
                <th class="text-center" style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($asignaciones as $a)
                @php
                    $p = $a->personal;
                    $registro = [
                        'apellidos' => $p->apellidos,
                        'nombres'   => $p->nombres,
                        'dni'       => $p->dni,
                        'celular'   => $p->celular,
                        'email'     => $p->email,
                        'direccion' => $p->direccion,
                        'cargo'     => $a->cargo,
                        'programa_estudio_id' => $a->programa_estudio_id,
                    ];
                @endphp
                <tr>
                    <td><span class="brand-chip-title">{{ $p->nombre_completo }}</span></td>
                    <td class="text-fecha">{{ $p->dni }}</td>
                    <td class="text-fecha">{{ $a->programaEstudio->nombre }}</td>
                    <td><span class="badge-cargo cargo-{{ strtolower($a->cargo) }}">{{ $a->cargo }}</span></td>
                    <td class="text-fecha">
                        {{ $p->celular ?: '-' }}
                        @if ($p->email)
                            <br>{{ $p->email }}
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon"
                                    data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                    data-url="{{ route('instituciones.personal.update', [$institucion, $a]) }}"
                                    data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                Editar
                            </button>

                            <form action="{{ route('instituciones.personal.destroy', [$institucion, $a]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Quitar a {{ addslashes($p->nombre_completo) }} como {{ $a->cargo }} de {{ addslashes($a->programaEstudio->nombre) }}?')">
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
                        <h6 class="text-white fw-bold mb-1">Esta institución aún no tiene personal</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Agregar Personal" para registrar coordinadores, docentes o asistentes.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modales')
{{-- Agregar programa de estudio --}}
<div class="modal fade" id="modalPrograma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form method="POST" action="{{ route('instituciones.programas.store', $institucion) }}">
                    @csrf

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Agregar Programa de Estudio</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <div class="campo">
                                    <label for="prog_nombre">Nombre del Programa</label>
                                    <input type="text" id="prog_nombre" name="nombre" maxlength="100" class="text-uppercase" placeholder="Ej. Desarrollo de Sistemas de Información" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="prog_abreviatura">Abreviatura</label>
                                    <input type="text" id="prog_abreviatura" name="abreviatura" maxlength="30" class="text-uppercase" placeholder="Ej. DSI">
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

{{-- Agregar / editar personal --}}
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form id="formRegistro" method="POST" action="{{ route('instituciones.personal.store', $institucion) }}"
                      data-url-nuevo="{{ route('instituciones.personal.store', $institucion) }}"
                      data-titulo-nuevo="Agregar Personal" data-titulo-editar="Editar Personal">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" disabled>

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2 class="js-titulo">Agregar Personal</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
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
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="cargo">Rol</label>
                                    <select id="cargo" name="cargo" required>
                                        @foreach ($cargos as $cargo)
                                            <option value="{{ $cargo }}">{{ ucfirst(strtolower($cargo)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="campo">
                                    <label for="programa_estudio_id">Programa de Estudio</label>
                                    <select id="programa_estudio_id" name="programa_estudio_id" required>
                                        @if ($programas->isEmpty())
                                            <option value="">Primero agregue un programa de estudio</option>
                                        @else
                                            <option value="">Seleccione un programa...</option>
                                            @foreach ($programas as $programa)
                                                <option value="{{ $programa->id }}">{{ $programa->nombre }}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
