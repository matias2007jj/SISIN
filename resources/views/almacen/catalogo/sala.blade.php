@extends(view()->exists('plantilla.plantilla') ? 'plantilla.plantilla' : 'plantilla')

@section('title', 'SISIN - Catálogo de Salas')
@section('titulo', 'Catálogo de Salas')
@section('boton', 'Nueva Sala')
@section('modal_id', 'modalAgregarSala')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marcas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
@endpush

@section('contenido')
@php
    $esSuper = auth()->user()->esSuperusuario();
    $cols = 4 + ($esSuper ? 1 : 0) + (($puedeEditar || $puedeEliminar) ? 1 : 0);
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 mb-4 text-white" style="background-color: rgba(16, 185, 129, 0.2); border-left: 4px solid #10b981 !important;" role="alert">
        <div class="d-flex align-items-center">
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 mb-4 text-white" style="background-color: rgba(255, 77, 77, 0.2); border-left: 4px solid #ff4d4d !important;" role="alert">
        <div class="d-flex align-items-center">
            <div>{{ $errors->first() }}</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Escribe para filtrar sala..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorSalas">{{ isset($salas) ? count($salas) : 0 }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaSalas">
        <thead>
            <tr>
                <th style="min-width: 220px;">Nombre Sala</th>
                @if ($esSuper)
                <th style="min-width: 180px;">Institución</th>
                @endif
                <th style="min-width: 180px;">Programa de Estudio</th>
                <th style="min-width: 150px;">Fecha Creación</th>
                <th style="min-width: 150px;">Fecha Modificación</th>
                @if ($puedeEditar || $puedeEliminar)
                <th class="text-center" style="width: 170px;">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if (isset($salas) && count($salas) > 0)
                @foreach ($salas as $sala)
                    <tr>
                        <td>
                            <div class="brand-chip">
                                <span class="brand-chip-title">{{ $sala->nombre }}</span>
                            </div>
                        </td>
                        @if ($esSuper)
                        <td>
                            @if(!empty($sala->institucion_nombre))
                                <span class="badge-programa">{{ $sala->institucion_nombre }}</span>
                            @else
                                <span class="text-muted fs-7">Sin institución</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            @if(!empty($sala->programa_estudio_nombre))
                                <span class="badge-programa">{{ $sala->programa_estudio_nombre }}</span>
                            @else
                                <span class="text-muted fs-7">General / N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-fecha">
                                {{ !empty($sala->created_at) ? \Carbon\Carbon::parse($sala->created_at)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-fecha">
                                {{ !empty($sala->updated_at) ? \Carbon\Carbon::parse($sala->updated_at)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </td>
                        @if ($puedeEditar || $puedeEliminar)
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                @if ($puedeEditar)
                                <button type="button" class="btn btn-outline-warning btn-action-icon" data-bs-toggle="modal" data-bs-target="#modalEditarSala{{ $sala->id }}" title="Editar Sala">
                                    Editar
                                </button>
                                @endif

                                @if ($puedeEliminar)
                                <form action="{{ route('catalogo.destroy', ['catalogo' => 'sala', 'id' => $sala->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar la sala {{ $sala->nombre }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action-icon" title="Eliminar Sala">
                                        Eliminar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr id="rowSinDatos">
                    <td colspan="{{ $cols }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay salas registradas</h6>
                        @if ($puedeCrear)
                        <p class="text-muted fs-7 mb-3">Haz clic en "Nueva Sala" para agregar la primera.</p>
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection

@section('modales')
<div class="modal fade" id="modalAgregarSala" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.store', ['catalogo' => 'sala']) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Nueva Sala</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="nombre">Nombre de la Sala</label>
                            <input type="text" id="nombre" name="nombre" class="text-uppercase form-control bg-dark text-white border-secondary" placeholder="Ej. LABORATORIO 1, AULA 204..." required autofocus>
                        </div>

                        @if(auth()->user()->esSuperusuario() || auth()->user()->tipo_usuario === 'ADMIN_INSTITUCION')
                        <div class="campo mb-3">
                            <label for="programa_estudio_id">Programa de Estudio</label>
                            <select id="programa_estudio_id" name="programa_estudio_id" class="form-select bg-dark text-white border-secondary" required>
                                <option value="">Seleccione un programa...</option>
                                @foreach($programasEstudio ?? [] as $prog)
                                    <option value="{{ $prog->id }}">
                                        {{ $prog->nombre }}{{ $esSuper && !empty($prog->institucion_nombre) ? ' (' . $prog->institucion_nombre . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if (empty($programasEstudio) || count($programasEstudio) === 0)
                                <small class="text-muted">Aún no hay programas de estudio. Regístrelos en "Mi institución" → Agregar Programa de Estudio.</small>
                            @endif
                        </div>
                        @endif

                        <div class="campo mb-3">
                            <label for="observacion">Ubicación / Observación</label>
                            <textarea id="observacion" name="observacion" rows="3" class="form-control bg-dark text-white border-secondary" placeholder="Ej. 2do piso, Pabellón B..."></textarea>
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

@foreach ($salas ?? [] as $sala)
<div class="modal fade" id="modalEditarSala{{ $sala->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.update', ['catalogo' => 'sala', 'id' => $sala->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Editar Sala</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="nombre_{{ $sala->id }}">Nombre de la Sala</label>
                            <input type="text" id="nombre_{{ $sala->id }}" name="nombre" class="text-uppercase form-control bg-dark text-white border-secondary" value="{{ $sala->nombre }}" required>
                        </div>

                        @if(auth()->user()->esSuperusuario() || auth()->user()->tipo_usuario === 'ADMIN_INSTITUCION')
                        <div class="campo mb-3">
                            <label for="programa_estudio_id_{{ $sala->id }}">Programa de Estudio</label>
                            <select id="programa_estudio_id_{{ $sala->id }}" name="programa_estudio_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($programasEstudio ?? [] as $prog)
                                    <option value="{{ $prog->id }}" {{ $sala->programa_estudio_id == $prog->id ? 'selected' : '' }}>
                                        {{ $prog->nombre }}{{ $esSuper && !empty($prog->institucion_nombre) ? ' (' . $prog->institucion_nombre . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="campo mb-3">
                            <label for="observacion_{{ $sala->id }}">Ubicación / Observación</label>
                            <textarea id="observacion_{{ $sala->id }}" name="observacion" rows="3" class="form-control bg-dark text-white border-secondary">{{ $sala->observacion ?? '' }}</textarea>
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
@endforeach
@endsection

@push('scripts')
<script src="{{ asset('js/modal.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('inputBuscar');
        const btnLimpiar = document.getElementById('btnLimpiarBuscar');
        const rows = document.querySelectorAll('#tablaSalas tbody tr:not(#rowSinDatos)');
        const contador = document.getElementById('contadorSalas');

        if (inputBuscar) {
            inputBuscar.addEventListener('keyup', function() {
                const filter = this.value.toUpperCase().trim();
                let visibles = 0;

                btnLimpiar.classList.toggle('d-none', filter === '');

                rows.forEach(row => {
                    const textContent = row.textContent || row.innerText;
                    if (textContent.toUpperCase().indexOf(filter) > -1) {
                        row.style.display = "";
                        visibles++;
                    } else {
                        row.style.display = "none";
                    }
                });

                if (contador) contador.textContent = visibles;
            });

            btnLimpiar.addEventListener('click', function() {
                inputBuscar.value = '';
                inputBuscar.dispatchEvent(new Event('keyup'));
                inputBuscar.focus();
            });
        }
    });
</script>
@endpush