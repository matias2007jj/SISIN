@extends(view()->exists('plantilla.plantilla') ? 'plantilla.plantilla' : 'plantilla')

@section('title', 'SISIN - Catálogo de Grupos')
@section('titulo', 'Catálogo de Grupos')
@section('icono', 'fa-people-group')
@section('boton', 'Nuevo Grupo')
@section('modal_id', 'modalAgregarGrupo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
@endpush

@section('contenido')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 mb-4 text-white" style="background-color: rgba(16, 185, 129, 0.2); border-left: 4px solid #10b981 !important;" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-circle-check fs-5 me-2 text-emerald"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<div class="row g-3 align-items-center justify-content-between mb-4">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group search-toolbar">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Escribe para filtrar grupo..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            <i class="fa-solid fa-boxes-stacked me-1"></i> Total Registros: <span id="contadorGrupos">{{ count($grupos) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaGrupos">
        <thead>
            <tr>
                <th style="min-width: 220px;">Nombre Grupo</th>
                <th style="min-width: 180px;">Programa de Estudio</th>
                <th style="min-width: 150px;">Fecha Creación</th>
                <th style="min-width: 150px;">Fecha Modificación</th>
                <th class="text-center" style="width: 110px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($grupos as $grupo)
                <tr>
                    <td>
                        <div class="brand-chip">
                            <span class="brand-chip-icon"><i class="fa-solid fa-users"></i></span>
                            <span class="brand-chip-title">{{ $grupo->nombre }}</span>
                        </div>
                    </td>
                    <td>
                        @if($grupo->programaEstudio)
                            <span class="badge-programa">
                                <i class="fa-solid fa-graduation-cap me-1"></i>{{ $grupo->programaEstudio->nombre }}
                            </span>
                        @else
                            <span class="text-muted fs-7">General / N/A</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-fecha">
                            <i class="fa-regular fa-calendar me-1 opacity-50"></i>
                            {{ $grupo->created_at ? $grupo->created_at->format('d/m/Y H:i') : '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-fecha">
                            <i class="fa-regular fa-clock me-1 opacity-50"></i>
                            {{ $grupo->updated_at ? $grupo->updated_at->format('d/m/Y H:i') : '-' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-outline-warning btn-action-icon" data-bs-toggle="modal" data-bs-target="#modalEditarGrupo{{ $grupo->id }}" title="Editar Grupo">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <form action="{{ route('catalogo.destroy', ['catalogo' => 'grupo', 'id' => $grupo->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar el grupo {{ $grupo->nombre }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action-icon" title="Eliminar Grupo">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="5" class="text-center py-5">
                        <i class="fa-solid fa-users fs-1 text-secondary opacity-25 mb-3 d-block"></i>
                        <h6 class="text-white fw-bold mb-1">No hay grupos registrados</h6>
                        <p class="text-muted fs-7 mb-3">Haz clic en "Nuevo Grupo" para agregar el primero.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modales')

<div class="modal fade" id="modalAgregarGrupo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.store', ['catalogo' => 'grupo']) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Nuevo Grupo</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="nombre">Nombre del Grupo</label>
                            <input type="text" id="nombre" name="nombre" class="text-uppercase form-control bg-dark text-white border-secondary" placeholder="Ingrese el nombre del grupo" required autofocus>
                        </div>

                        <div class="campo mb-3">
                            <label for="programa_estudio_id">Programa de Estudio</label>
                            <select id="programa_estudio_id" name="programa_estudio_id" class="form-select bg-dark text-white border-secondary" required>
                                <option value="">Seleccione un programa...</option>
                                @foreach($programasEstudio ?? [] as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->nombre }}</option>
                                @endforeach
                            </select>
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

@foreach ($grupos as $grupo)
<div class="modal fade" id="modalEditarGrupo{{ $grupo->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.update', ['catalogo' => 'grupo', 'id' => $grupo->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Editar Grupo</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="nombre_{{ $grupo->id }}">Nombre</label>
                            <input type="text" id="nombre_{{ $grupo->id }}" name="nombre" class="text-uppercase form-control bg-dark text-white border-secondary" value="{{ $grupo->nombre }}" required>
                        </div>

                        <div class="campo mb-3">
                            <label for="programa_estudio_id_{{ $grupo->id }}">Programa de Estudio</label>
                            <select id="programa_estudio_id_{{ $grupo->id }}" name="programa_estudio_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($programasEstudio ?? [] as $prog)
                                    <option value="{{ $prog->id }}" {{ $grupo->programa_estudio_id == $prog->id ? 'selected' : '' }}>
                                        {{ $prog->nombre }}
                                    </option>
                                @endforeach
                            </select>
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
        const rows = document.querySelectorAll('#tablaGrupos tbody tr:not(#rowSinDatos)');
        const contador = document.getElementById('contadorGrupos');

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