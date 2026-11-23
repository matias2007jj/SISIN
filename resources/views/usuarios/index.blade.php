@extends('plantilla.plantilla')

@section('title', 'SISIN - Usuarios y Personal')
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
        Está viendo todas las instituciones. Para crear usuarios, elija una en el menú de usuario → <strong>Institución activa</strong>.
    </div>
@endif

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por nombre, DNI, usuario, rol o programa..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorRegistros">{{ count($usuarios) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                <th style="min-width: 210px;">Apellidos y Nombres</th>
                <th style="width: 95px;">DNI</th>
                <th style="min-width: 155px;">Usuario</th>
                @if ($sinInstitucion)
                    <th style="min-width: 230px;">Institución</th>
                @endif
                <th style="min-width: 195px;">Rol</th>
                <th style="min-width: 185px;">Programas de estudio</th>
                <th style="min-width: 165px;">Contacto</th>
                <th class="text-center" style="width: 100px;">Estado</th>
                <th class="text-center" style="width: 175px; min-width: 175px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $u)
                @php
                    $p = $u->personal;
                    $ids = $u->programas->where('pivot.acceso', true)->pluck('id')->values();
                    $registro = [
                        'apellidos'    => $p?->apellidos ?? '',
                        'nombres'      => $p?->nombres ?? '',
                        'dni'          => $p?->dni ?? '',
                        'celular'      => $p?->celular ?? '',
                        'email'        => $p?->email ?? '',
                        'direccion'    => $p?->direccion ?? '',
                        'username'     => $u->username,
                        'tipo_usuario' => $u->tipo_usuario,
                        'activo'       => $u->activo,
                        'programas'    => $ids,
                    ];
                @endphp
                <tr>
                    <td>
                        <span class="brand-chip-title">{{ $p?->nombre_completo ?? '-' }}</span>
                    </td>
                    <td class="text-fecha text-nowrap">{{ $p?->dni ?? '-' }}</td>
                    <td class="text-fecha">{{ $u->username }}</td>
                    @if ($sinInstitucion)
                        <td class="text-fecha">{{ $u->institucion->nombre ?? '-' }}</td>
                    @endif
                    <td class="text-nowrap">
                        <span class="badge-rol">{{ $u->rol_etiqueta }}</span>
                    </td>
                    <td class="text-fecha">
                        @if ($u->tipo_usuario === \App\Models\Usuario::ADMIN_INSTITUCION)
                            Todos
                        @else
                            {{ $u->programas->where('pivot.acceso', true)->pluck('nombre')->join(', ') ?: 'Sin acceso' }}
                        @endif
                    </td>
                    <td class="text-fecha">
                        {{ $p?->celular ?: '-' }}
                        @if ($p?->email)
                            <br>{{ $p->email }}
                        @endif
                    </td>
                    <td class="text-center text-nowrap">
                        <span class="badge-rol {{ $u->activo ? '' : 'badge-inactivo' }}">{{ $u->activo ? 'ACTIVO' : 'INACTIVO' }}</span>
                    </td>
                    <td class="text-center text-nowrap">
                        <div class="d-inline-flex gap-1">
                            @if ($u->tipo_usuario === \App\Models\Usuario::ADMIN_INSTITUCION && ! auth()->user()->esSuperusuario())
                                <span class="text-muted fs-7">Lo gestiona el superusuario</span>
                            @else
                                <button type="button" class="btn btn-outline-warning btn-action-icon"
                                        data-bs-toggle="modal" data-bs-target="#modalRegistro"
                                        data-url="{{ route('usuarios.update', $u) }}"
                                        data-registro="{{ json_encode($registro, JSON_UNESCAPED_UNICODE) }}">
                                    Editar
                                </button>
                                @if ($u->id !== auth()->id())
                                    <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar a {{ addslashes($p?->nombre_completo ?? $u->username) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action-icon">Eliminar</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $sinInstitucion ? 9 : 8 }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay usuarios registrados</h6>
                        <p class="text-muted fs-7 mb-0">Haz clic en "Nuevo Usuario" para registrar al personal con su usuario, rol y programas.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modales')
{{-- Modal Crear / Editar Usuario --}}
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <div class="js-bloque-personal">
                            <p class="asignaciones-titulo mb-2">1. Datos Personales</p>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <div class="campo">
                                        <label for="apellidos">Apellidos</label>
                                        <input type="text" id="apellidos" name="apellidos" maxlength="60"
                                               pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ' -]+"
                                               title="Solo se permiten letras y espacios (sin números)"
                                               class="text-uppercase js-req-personal" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="campo">
                                        <label for="nombres">Nombres</label>
                                        <input type="text" id="nombres" name="nombres" maxlength="60"
                                               pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ' -]+"
                                               title="Solo se permiten letras y espacios (sin números)"
                                               class="text-uppercase js-req-personal" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="campo">
                                        <label for="dni">DNI</label>
                                        <input type="text" id="dni" name="dni" maxlength="8" minlength="8"
                                               inputmode="numeric" pattern="[0-9]{8}"
                                               title="El DNI debe tener exactamente 8 dígitos numéricos"
                                               placeholder="8 dígitos" class="js-req-personal" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="campo">
                                        <label for="celular">Celular</label>
                                        <input type="text" id="celular" name="celular" maxlength="9" minlength="9"
                                               inputmode="numeric" pattern="[0-9]{9}"
                                               title="El celular debe tener exactamente 9 dígitos numéricos"
                                               placeholder="9 dígitos">
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
                        </div>

                        <p class="asignaciones-titulo mb-2">2. Credenciales y Rol</p>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="username">Usuario</label>
                                    <input type="text" id="username" name="username" maxlength="100" autocomplete="off" placeholder="Vacío = DNI">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="password">Contraseña</label>
                                    <input type="text" id="password" name="password" maxlength="60" autocomplete="new-password"
                                           placeholder="Vacío = generar automática">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="campo">
                                    <label for="tipo_usuario">Rol</label>
                                    <select id="tipo_usuario" name="tipo_usuario" required>
                                        @foreach ($roles as $valor => $etiqueta)
                                            <option value="{{ $valor }}" @selected($valor === 'DOCENTE')>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="js-bloque-programas">
                            <p class="asignaciones-titulo mb-2">3. Programas de Estudio con Acceso</p>
                            <div class="campo mb-3">
                                <div class="lista-programas">
                                    @forelse ($programas as $prog)
                                        <label><input type="checkbox" name="programas[]" value="{{ $prog->id }}"> {{ $prog->nombre }}</label>
                                    @empty
                                        <span class="text-muted fs-7">La institución aún no tiene programas. Regístrelos primero en Mi institución.</span>
                                    @endforelse
                                </div>
                                <small class="text-muted">Seleccione uno o más programas de estudio a los que tendrá acceso este usuario.</small>
                            </div>
                        </div>

                        <div class="js-solo-editar d-none form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" checked>
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