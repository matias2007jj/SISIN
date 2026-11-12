@extends('plantilla.plantilla')

@section('title', 'SISIN - Usuarios')
@section('titulo', 'Usuarios')
@section('boton', 'Nuevo Usuario')
@section('modal_id', 'modalRegistro')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
@endpush

@section('contenido')
@include('organizacion._alertas')

@if ($sinInstitucion)
    <div class="alerta-sisin error" role="alert">
        Está viendo todas las instituciones. Para crear un usuario, elija una en el menú de usuario → <strong>Institución activa</strong>.
    </div>
@endif

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por usuario, nombre, DNI o rol..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">Total Registros: <span id="contadorRegistros">{{ count($usuarios) }}</span></span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th>Usuario</th>
                <th style="min-width: 220px;">Persona</th>
                @if ($sinInstitucion)<th>Institución</th>@endif
                <th>Rol</th>
                <th style="min-width: 200px;">Programas con acceso</th>
                <th class="text-center">Estado</th>
                <th class="text-center" style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $u)
                @php
                    $ids = $u->programas->where('pivot.acceso', true)->pluck('id')->values();
                    $registro = [
                        'tipo_usuario' => $u->tipo_usuario,
                        'activo'       => $u->activo,
                        'programas'    => $ids,
                        'persona'      => $u->personal?->nombre_completo ?? $u->username,
                    ];
                @endphp
                <tr>
                    <td class="text-fecha">{{ $u->username }}</td>
                    <td>
                        {{ $u->personal?->nombre_completo ?? '-' }}
                        @if ($u->personal)<div class="text-muted fs-7">DNI {{ $u->personal->dni }}</div>@endif
                    </td>
                    @if ($sinInstitucion)<td class="text-fecha">{{ $u->institucion->nombre ?? '-' }}</td>@endif
                    <td><span class="badge-rol">{{ $u->rol_etiqueta }}</span></td>
                    <td class="text-fecha">
                        @if ($u->tipo_usuario === \App\Models\Usuario::ADMIN_INSTITUCION)
                            Todos
                        @else
                            {{ $u->programas->where('pivot.acceso', true)->pluck('nombre')->join(', ') ?: 'Sin acceso' }}
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-rol {{ $u->activo ? '' : 'badge-inactivo' }}">{{ $u->activo ? 'ACTIVO' : 'INACTIVO' }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon"
                                    data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                    data-url="{{ route('usuarios.update', $u) }}"
                                    data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                Editar
                            </button>
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar el usuario {{ $u->username }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action-icon">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $sinInstitucion ? 7 : 6 }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay usuarios registrados</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Nuevo Usuario" para darle acceso a una persona del personal.</p>
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
                <form id="formRegistro" method="POST" action="{{ route('usuarios.store') }}"
                      data-url-nuevo="{{ route('usuarios.store') }}"
                      data-titulo-nuevo="Nuevo Usuario" data-titulo-editar="Editar Usuario">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" disabled>

                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2 class="js-titulo">Nuevo Usuario</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        {{-- Solo al crear: elegir a la persona --}}
                        <div class="js-solo-nuevo">
                            <div class="campo mb-3">
                                <label for="personal_id">Persona del personal</label>
                                <select id="personal_id" name="personal_id">
                                    <option value="">Seleccione...</option>
                                    @foreach ($personal as $p)
                                        @php
                                            $cargos = $p->asignaciones->pluck('cargo');
                                            $rolSugerido = $cargos->contains('COORDINADOR') ? 'COORDINADOR'
                                                         : ($cargos->contains('ASISTENTE') ? 'ASISTENTE' : 'CONSULTA');
                                        @endphp
                                        <option value="{{ $p->id }}" data-dni="{{ $p->dni }}" data-rol="{{ $rolSugerido }}">
                                            {{ $p->nombre_completo }} — {{ $p->dni }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($personal->isEmpty())
                                    <small class="text-muted">No hay personal sin usuario. Registre primero al personal en Organización → Personal.</small>
                                @endif
                            </div>
                            <div class="campo mb-3">
                                <label for="username">Usuario</label>
                                <input type="text" id="username" name="username" maxlength="15" autocomplete="off" placeholder="Vacío = DNI">
                            </div>
                        </div>

                        {{-- Solo al editar: a quién pertenece --}}
                        <p class="js-solo-editar d-none text-muted fs-7 mb-3">Persona: <strong class="text-white js-persona"></strong></p>

                        <div class="campo mb-3">
                            <label for="password">Contraseña</label>
                            <input type="text" id="password" name="password" maxlength="60" autocomplete="new-password"
                                   placeholder="Vacío = generar una automática (al editar: no cambiarla)">
                        </div>

                        <div class="campo mb-3">
                            <label for="tipo_usuario">Rol</label>
                            <select id="tipo_usuario" name="tipo_usuario" required>
                                @foreach ($roles as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected($valor === 'CONSULTA')>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="campo mb-3">
                            <label>Programas de estudio con acceso</label>
                            <div class="lista-programas">
                                @forelse ($programas as $prog)
                                    <label><input type="checkbox" name="programas[]" value="{{ $prog->id }}"> {{ $prog->nombre }}</label>
                                @empty
                                    <span class="text-muted fs-7">La institución aún no tiene programas.</span>
                                @endforelse
                            </div>
                            <small class="text-muted">El administrador de institución accede a todos los programas.</small>
                        </div>

                        <div class="js-solo-editar d-none form-check">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                            <label class="form-check-label text-white" for="activo">Usuario activo (puede iniciar sesión)</label>
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
<script src="{{ asset('js/usuarios.js') }}"></script>
@endpush
