@extends(view()->exists('plantilla.plantilla') ? 'plantilla.plantilla' : 'plantilla')

@section('title', 'SISIN - Catálogo de Modelos')
@section('titulo', 'Catálogo de Modelos')
@section('boton', 'Nuevo Modelo')
@section('modal_id', 'modalAgregarModelo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marcas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
@endpush

@section('contenido')
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
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Escribe para filtrar modelo..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">
                &times;
            </button>
        </div>
    </div>

    <div class="col-12 col-md-6 text-md-end">
        <span class="badge-count">
            Total Registros: <span id="contadorModelos">{{ count($modelos) }}</span>
        </span>
    </div>
</div>

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaModelos">
        <thead>
            <tr>
                <th style="min-width: 220px;">Nombre Modelo</th>
                <th style="min-width: 180px;">Programa de Estudio</th>
                <th style="min-width: 150px;">Fecha Creación</th>
                <th style="min-width: 150px;">Fecha Modificación</th>
                @if ($puedeEditar || $puedeEliminar)
                <th class="text-center" style="width: 170px;">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($modelos as $modelo)
                <tr>
                    <td>
                        <div class="brand-chip">
                            <span class="brand-chip-title">{{ $modelo->nombre }}</span>
                        </div>
                    </td>
                    <td>
                        @if(!empty($modelo->programa_estudio_nombre))
                            <span class="badge-programa">
                                {{ $modelo->programa_estudio_nombre }}
                            </span>
                        @else
                            <span class="text-muted fs-7">General / N/A</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-fecha">
                            
                            {{ !empty($modelo->created_at) ? \Carbon\Carbon::parse($modelo->created_at)->format('d/m/Y H:i') : '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-fecha">
                            
                            {{ !empty($modelo->updated_at) ? \Carbon\Carbon::parse($modelo->updated_at)->format('d/m/Y H:i') : '-' }}
                        </span>
                    </td>
                    @if ($puedeEditar || $puedeEliminar)
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            @if ($puedeEditar)
                            <button type="button" class="btn btn-outline-warning btn-action-icon" data-bs-toggle="modal" data-bs-target="#modalEditarModelo{{ $modelo->id }}" title="Editar Modelo">
                                Editar
                            </button>
                            @endif

                            @if ($puedeEliminar)
                            <form action="{{ route('catalogo.destroy', ['catalogo' => 'modelo', 'id' => $modelo->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar el modelo {{ $modelo->nombre }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action-icon" title="Eliminar Modelo">
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ ($puedeEditar || $puedeEliminar) ? 5 : 4 }}" class="text-center py-5">
                        
                        <h6 class="text-white fw-bold mb-1">No hay modelos registrados</h6>
                        @if ($puedeCrear)
                        <p class="text-muted fs-7 mb-3">Haz clic en "Nuevo Modelo" para agregar el primero.</p>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('modales')
<div class="modal fade" id="modalAgregarModelo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.store', ['catalogo' => 'modelo']) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Nuevo registro</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="text-uppercase" placeholder="Ingrese el nombre" required autofocus>
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

@foreach ($modelos as $modelo)
<div class="modal fade" id="modalEditarModelo{{ $modelo->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            <div class="mi-modal">
                <form action="{{ route('catalogo.update', ['catalogo' => 'modelo', 'id' => $modelo->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <div class="titulo">
                            <span class="punto"></span>
                            <h2>Editar registro</h2>
                        </div>
                        <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="campo">
                            <label for="nombre_{{ $modelo->id }}">Nombre</label>
                            <input type="text" id="nombre_{{ $modelo->id }}" name="nombre" class="text-uppercase" value="{{ $modelo->nombre }}" placeholder="Ingrese el nombre" required>
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
<script src="{{ asset('js/modelo.js') }}"></script>
@endpush