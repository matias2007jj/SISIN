@extends(view()->exists('plantilla.plantilla') ? 'plantilla.plantilla' : 'plantilla')

@section('title', 'SISIN - Catálogo de Tipos')
@section('titulo', 'Catálogo de Tipos')
@section('boton', 'Nuevo Tipo')
@section('modal_id', 'modalAgregarTipo')

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

<div class="row g-3 align-items-center justify-content-between mb-3">
    <div class="col-12 col-xl-5">
        <div class="input-group search-toolbar">
            <input
                type="text"
                id="inputBuscar"
                class="form-control text-uppercase"
                placeholder="Escribe para filtrar tipo..."
                autocomplete="off"
                data-tabla="#tablaTipos tbody tr:not(#rowSinDatos)"
                data-contador="#contadorTipos"
            >
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>

    <div class="col-12 col-xl-7 d-flex flex-wrap align-items-center justify-content-xl-end gap-2">
        @if ($puedeCrear)
            <button type="button" class="btn btn-outline-light btn-sm px-3 py-2"
                    data-bs-toggle="modal" data-bs-target="#modalImportarExcel">
                Importar Excel
            </button>
        @endif

        <a href="{{ route('catalogo.exportar.excel', ['catalogo' => 'tipo']) }}"
           data-base-url="{{ route('catalogo.exportar.excel', ['catalogo' => 'tipo']) }}"
           class="btn btn-outline-success btn-sm px-3 py-2 js-btn-exportar">
            Exportar Excel
        </a>

        <a href="{{ route('catalogo.exportar.pdf', ['catalogo' => 'tipo']) }}" target="_blank"
           data-base-url="{{ route('catalogo.exportar.pdf', ['catalogo' => 'tipo']) }}"
           class="btn btn-outline-danger btn-sm px-3 py-2 js-btn-exportar">
            Exportar PDF
        </a>

        <span class="badge-count ms-xl-2">
            Total Registros: <span id="contadorTipos">{{ count($tipos) }}</span>
        </span>
    </div>
</div>

@include('almacen.catalogo._filtros')

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaTipos">
        <thead>
            <tr>
                <th style="min-width: 220px;">Nombre Tipo</th>
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
            @if (count($tipos) > 0)
                @foreach ($tipos as $tipo)
                    <tr data-institucion-id="{{ $tipo->institucion_id ?? '' }}" data-programa-id="{{ $tipo->programa_estudio_id ?? '' }}">
                        <td>
                            <div class="brand-chip">
                                <span class="brand-chip-title">{{ $tipo->nombre }}</span>
                            </div>
                        </td>
                        @if ($esSuper)
                        <td>
                            @if(!empty($tipo->institucion_nombre))
                                <span class="badge-programa">{{ $tipo->institucion_nombre }}</span>
                            @else
                                <span class="text-muted fs-7">Sin institución</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            @if(!empty($tipo->programa_estudio_nombre))
                                <span class="badge-programa">{{ $tipo->programa_estudio_nombre }}</span>
                            @else
                                <span class="text-muted fs-7">General / N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-fecha">
                                {{ !empty($tipo->created_at) ? \Carbon\Carbon::parse($tipo->created_at)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-fecha">
                                {{ !empty($tipo->updated_at) ? \Carbon\Carbon::parse($tipo->updated_at)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </td>
                        @if ($puedeEditar || $puedeEliminar)
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                @if ($puedeEditar)
                                <button type="button" class="btn btn-outline-warning btn-action-icon" data-bs-toggle="modal" data-bs-target="#modalEditarTipo{{ $tipo->id }}" title="Editar Tipo">
                                    Editar
                                </button>
                                @endif

                                @if ($puedeEliminar)
                                <form action="{{ route('catalogo.destroy', ['catalogo' => 'tipo', 'id' => $tipo->id]) }}" method="POST" class="d-inline" data-confirm="¿Desea eliminar el tipo {{ $tipo->nombre }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action-icon" title="Eliminar Tipo">
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
                        <h6 class="text-white fw-bold mb-1">No hay tipos registrados</h6>
                        @if ($puedeCrear)
                        <p class="text-muted fs-7 mb-3">Haz clic en "Nuevo Tipo" para agregar el primero.</p>
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection

@section('modales')
<div class="modal fade" id="modalAgregarTipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.store', ['catalogo' => 'tipo']) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Nuevo Tipo</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="nombre">Nombre del Tipo</label>
                            <input type="text" id="nombre" name="nombre" class="text-uppercase form-control bg-dark text-white border-secondary" placeholder="Ingrese el nombre del tipo" required autofocus>
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

@foreach ($tipos as $tipo)
<div class="modal fade" id="modalEditarTipo{{ $tipo->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.update', ['catalogo' => 'tipo', 'id' => $tipo->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Editar Tipo</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="nombre_{{ $tipo->id }}">Nombre</label>
                            <input type="text" id="nombre_{{ $tipo->id }}" name="nombre" class="text-uppercase form-control bg-dark text-white border-secondary" value="{{ $tipo->nombre }}" required>
                        </div>

                        @if(auth()->user()->esSuperusuario() || auth()->user()->tipo_usuario === 'ADMIN_INSTITUCION')
                        <div class="campo mb-3">
                            <label for="programa_estudio_id_{{ $tipo->id }}">Programa de Estudio</label>
                            <select id="programa_estudio_id_{{ $tipo->id }}" name="programa_estudio_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($programasEstudio ?? [] as $prog)
                                    <option value="{{ $prog->id }}" {{ $tipo->programa_estudio_id == $prog->id ? 'selected' : '' }}>
                                        {{ $prog->nombre }}{{ $esSuper && !empty($prog->institucion_nombre) ? ' (' . $prog->institucion_nombre . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
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

@if ($puedeCrear)
<div class="modal fade" id="modalImportarExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form id="formImportarExcel" method="POST" action="{{ route('catalogo.importar.excel', ['catalogo' => 'tipo']) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Importar Tipos desde Excel</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo mb-3">
                            <label for="archivo_excel">Archivo Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls,.csv" required>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Columnas: nombre, programa_estudio</small>
                        </div>

                        <a href="{{ route('catalogo.plantilla.excel', ['catalogo' => 'tipo']) }}" class="btn btn-outline-info btn-sm">
                            Descargar plantilla Excel
                        </a>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-guardar">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/catalogo.js') }}"></script>
    <script src="{{ asset('js/exportar.js') }}"></script>
@endpush