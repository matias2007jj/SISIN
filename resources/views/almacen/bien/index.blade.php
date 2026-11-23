@extends('plantilla.plantilla')

@section('title', 'SISIN - Bienes')
@section('titulo', 'Bienes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bien.css') }}">
@endpush

@section('contenido')
@include('organizacion._alertas')

@php
    $user   = auth()->user();
    $global = $user->esSuperusuario() && ! $user->institucionActiva();

    $columnaPrograma  = $global || $verInstitucion;
    $mostrarAcciones  = ! $global && ($puedeRegistrar || $puedeInactivar);
    $columnas         = 9 + ($global ? 1 : 0) + ($columnaPrograma ? 1 : 0) - ($mostrarAcciones ? 0 : 1);

    $puedeCrearSala   = $user->puede('catalogo.sala.crear')   || $puedeRegistrar;
    $puedeCrearGrupo  = $user->puede('catalogo.grupo.crear')  || $puedeRegistrar;
    $puedeCrearTipo   = $user->puede('catalogo.tipo.crear')   || $puedeRegistrar;
    $puedeCrearMarca  = $user->puede('catalogo.marca.crear')  || $puedeRegistrar;
    $puedeCrearModelo = $user->puede('catalogo.modelo.crear') || $puedeRegistrar;

    $mostrarFiltroPrograma = ($global || $verInstitucion) && isset($programasFiltro) && count($programasFiltro) > 0;
    $mostrarFiltroSala     = isset($salasFiltro) && count($salasFiltro) > 0;
@endphp

<div class="row g-3 align-items-center justify-content-between mb-3">
    <div class="col-12 col-md-5 col-lg-4">
        <div class="input-group search-toolbar">
            <input type="text" id="inputBuscar" class="form-control text-uppercase" placeholder="Filtrar por CBI, descripción, sala..." autocomplete="off">
            <button type="button" class="btn btn-link text-muted pe-1 d-none" id="btnLimpiarBuscar">&times;</button>
        </div>
    </div>
    <div class="col-12 col-md-7 text-md-end d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <span class="badge-count">Total Registros: <span id="contadorRegistros">{{ count($bienes) }}</span></span>

        <a href="{{ route('bienes.exportar.excel') }}" class="btn btn-outline-success btn-action-icon">
            Exportar Excel
        </a>

        @if (!$global && $puedeRegistrar)
            <button type="button" class="btn btn-outline-info btn-action-icon" data-bs-toggle="modal" data-bs-target="#modalImportarBien">
                Importar Excel
            </button>

            <button type="button" class="btn btn-success btn-action-icon" id="btnNuevoBien" style="background-color:#10b981; border:none;">
                + Nuevo Bien
            </button>
        @endif
    </div>
</div>

@if ($mostrarFiltroPrograma || $mostrarFiltroSala)
<div class="row g-3 align-items-end mb-4">
    @if ($mostrarFiltroPrograma)
    <div class="col-12 col-md-4 col-lg-3">
        <label for="filtroProgramaBien" class="form-label text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; color: #94a3b8; letter-spacing: 0.5px;">
            Programa de Estudios:
        </label>
        <select id="filtroProgramaBien" class="form-select form-select-sm bg-dark text-white border-secondary text-uppercase" style="height: 38px;">
            <option value="">TODOS LOS PROGRAMAS</option>
            @foreach ($programasFiltro as $prog)
                <option value="{{ $prog->id }}">{{ $prog->nombre }}</option>
            @endforeach
        </select>
    </div>
    @endif

    @if ($mostrarFiltroSala)
    <div class="col-12 col-md-4 col-lg-3">
        <label for="filtroSalaBien" class="form-label text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; color: #94a3b8; letter-spacing: 0.5px;">
            Salas:
        </label>
        <select id="filtroSalaBien" class="form-select form-select-sm bg-dark text-white border-secondary text-uppercase" style="height: 38px;">
            <option value="">TODAS LAS SALAS</option>
            @foreach ($salasFiltro as $s)
                <option value="{{ $s->id }}" data-programa-id="{{ $s->programa_estudio_id ?? '' }}">
                    {{ $s->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="col-12 col-md-auto">
        <button type="button" id="btnLimpiarFiltrosBien" class="btn btn-outline-secondary btn-sm px-3 d-none" style="height: 38px;">
            Limpiar filtros
        </button>
    </div>
</div>
@endif

<div class="table-responsive table-sisin-wrapper">
    <table class="table table-sisin align-middle" id="tablaRegistros">
        <thead>
            <tr>
                @if ($global)
                    <th>Institución</th>
                @endif
                @if ($columnaPrograma)
                    <th>Programa</th>
                @endif
                <th>CBI</th>
                <th>Descripción</th>
                <th>Sala</th>
                <th>Grupo</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Estado</th>
                @if ($mostrarAcciones)
                    <th class="text-center">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($bienes as $b)
                <tr class="{{ $b->estado_id == 6 ? 'opacity-50' : '' }}"
                    data-programa-id="{{ $b->programa_estudio_id ?? '' }}"
                    data-sala-id="{{ $b->sala_id ?? '' }}">
                    @if ($global)
                        <td class="text-fecha">{{ $b->institucion_nombre ?? '-' }}</td>
                    @endif
                    @if ($columnaPrograma)
                        <td><span class="badge-programa">{{ $b->programa_estudio_nombre ?? '-' }}</span></td>
                    @endif
                    <td class="text-fecha fw-bold">{{ $b->cbi }}</td>
                    <td class="fw-semibold text-white">{{ $b->descripcion }}</td>
                    <td>{{ $b->sala_nombre ?? '-' }}</td>
                    <td>{{ $b->grupo_nombre ?? '-' }}</td>
                    <td>{{ $b->tipo_nombre ?? '-' }}</td>
                    <td>{{ $b->marca_nombre ?? '-' }}</td>
                    <td>{{ $b->modelo_nombre ?? '-' }}</td>
                    <td>
                        @if ($b->estado_id == 6)
                            <span class="badge bg-danger">{{ $b->estado_nombre }}</span>
                        @else
                            <span class="badge bg-success">{{ $b->estado_nombre }}</span>
                        @endif
                    </td>
                    @if ($mostrarAcciones)
                        <td class="text-center text-nowrap">
                            @if ($puedeRegistrar && ($b->estado_id != 6 || $puedeInactivar))
                                <button type="button" class="btn btn-outline-warning btn-action-icon btn-editar"
                                    data-bien='@json($b)'
                                    data-url="{{ route('bienes.update', $b->id) }}">
                                    Editar
                                </button>
                            @endif

                            @if ($puedeInactivar && $b->estado_id != 6)
                                <form action="{{ route('bienes.destroy', $b->id) }}" method="POST" class="d-inline form-desactivar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action-icon">
                                        Inactivar
                                    </button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr id="rowSinDatos">
                    <td colspan="{{ $columnas }}" class="text-center py-5">
                        <h6 class="text-white fw-bold mb-1">No hay bienes registrados</h6>
                        <p class="text-muted fs-7 mb-0">Este programa de estudio todavía no tiene bienes.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (!$global && $puedeRegistrar)
{{-- Modal para Importar Excel con Formato Oficial --}}
<div class="modal fade" id="modalImportarBien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('bienes.importar.excel') }}" enctype="multipart/form-data" class="modal-content mi-modal">
            @csrf
            <div class="modal-header">
                <div class="titulo">
                    <span class="punto"></span>
                    <h2>Importar Bienes desde Excel</h2>
                </div>
                <button type="button" class="cerrar" data-bs-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size: 0.9rem;">
                    Solo se aceptan archivos <strong>.xlsx</strong> que respeten exactamente la estructura y encabezados del formato oficial.
                </p>

                <div class="mb-3">
                    <a href="{{ route('bienes.plantilla.excel') }}" class="btn btn-outline-success btn-sm">
                        Descargar Plantilla Oficial (.xlsx)
                    </a>
                </div>

                <div class="campo">
                    <label for="archivoExcelBien">Seleccionar archivo (.xlsx) *</label>
                    <input
                        type="file"
                        name="archivo"
                        id="archivoExcelBien"
                        accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                        required
                    >
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-guardar">Importar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Nuevo / Editar Bien --}}
<div class="modal fade" id="modalBien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formBien" method="POST" action="{{ route('bienes.store') }}" data-store-url="{{ route('bienes.store') }}" class="modal-content mi-modal">
            @csrf
            <input type="hidden" name="_method" id="metodoForm" value="POST">

            <div class="modal-header">
                <div class="titulo">
                    <span class="punto"></span>
                    <h2 id="tituloModalBien">Nuevo Bien</h2>
                </div>
                <button type="button" class="cerrar" data-bs-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4 campo">
                        <label for="cbi">CBI *</label>
                        <input type="text" name="cbi" id="cbi" maxlength="10" class="text-uppercase" placeholder="EJ. 000123" required>
                    </div>
                    <div class="col-md-8 campo">
                        <label for="descripcion">Descripción *</label>
                        <input type="text" name="descripcion" id="descripcion" maxlength="100" class="text-uppercase" placeholder="INGRESE LA DESCRIPCIÓN DEL BIEN" required>
                    </div>

                    <div class="col-md-6 campo">
                        <label for="sala_id">Sala *</label>
                        <div class="select-con-boton">
                            <select name="sala_id" id="sala_id" required>
                                <option value="">Seleccione una sala...</option>
                                @foreach ($salas as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($puedeCrearSala)
                                <button type="button"
                                    class="btn btn-outline-success btn-add-rapido js-btn-catalogo-rapido"
                                    title="Agregar nueva Sala"
                                    data-catalogo="sala"
                                    data-titulo="Nueva Sala"
                                    data-label="Nombre de la Sala"
                                    data-placeholder="EJ. LABORATORIO 1, AULA 204..."
                                    data-select="#sala_id"
                                    data-url="{{ route('catalogo.store', ['catalogo' => 'sala']) }}">
                                    +
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6 campo">
                        <label for="grupo_id">Grupo *</label>
                        <div class="select-con-boton">
                            <select name="grupo_id" id="grupo_id" required>
                                <option value="">Seleccione un grupo...</option>
                                @foreach ($grupos as $g)
                                    <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($puedeCrearGrupo)
                                <button type="button"
                                    class="btn btn-outline-success btn-add-rapido js-btn-catalogo-rapido"
                                    title="Agregar nuevo Grupo"
                                    data-catalogo="grupo"
                                    data-titulo="Nuevo Grupo"
                                    data-label="Nombre del Grupo"
                                    data-placeholder="INGRESE EL NOMBRE DEL GRUPO"
                                    data-select="#grupo_id"
                                    data-url="{{ route('catalogo.store', ['catalogo' => 'grupo']) }}">
                                    +
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="tipo_id">Tipo *</label>
                        <div class="select-con-boton">
                            <select name="tipo_id" id="tipo_id" required>
                                <option value="">Seleccione un tipo...</option>
                                @foreach ($tipos as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($puedeCrearTipo)
                                <button type="button"
                                    class="btn btn-outline-success btn-add-rapido js-btn-catalogo-rapido"
                                    title="Agregar nuevo Tipo"
                                    data-catalogo="tipo"
                                    data-titulo="Nuevo Tipo"
                                    data-label="Nombre del Tipo"
                                    data-placeholder="INGRESE EL NOMBRE DEL TIPO"
                                    data-select="#tipo_id"
                                    data-url="{{ route('catalogo.store', ['catalogo' => 'tipo']) }}">
                                    +
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="marca_id">Marca</label>
                        <div class="select-con-boton">
                            <select name="marca_id" id="marca_id">
                                <option value="">Sin marca</option>
                                @foreach ($marcas as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($puedeCrearMarca)
                                <button type="button"
                                    class="btn btn-outline-success btn-add-rapido js-btn-catalogo-rapido"
                                    title="Agregar nueva Marca"
                                    data-catalogo="marca"
                                    data-titulo="Nueva Marca"
                                    data-label="Nombre de la Marca"
                                    data-placeholder="INGRESE EL NOMBRE DE LA MARCA"
                                    data-select="#marca_id"
                                    data-url="{{ route('catalogo.store', ['catalogo' => 'marca']) }}">
                                    +
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="modelo_id">Modelo</label>
                        <div class="select-con-boton">
                            <select name="modelo_id" id="modelo_id">
                                <option value="">Sin modelo</option>
                                @foreach ($modelos as $mo)
                                    <option value="{{ $mo->id }}">{{ $mo->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($puedeCrearModelo)
                                <button type="button"
                                    class="btn btn-outline-success btn-add-rapido js-btn-catalogo-rapido"
                                    title="Agregar nuevo Modelo"
                                    data-catalogo="modelo"
                                    data-titulo="Nuevo Modelo"
                                    data-label="Nombre del Modelo"
                                    data-placeholder="INGRESE EL NOMBRE DEL MODELO"
                                    data-select="#modelo_id"
                                    data-url="{{ route('catalogo.store', ['catalogo' => 'modelo']) }}">
                                    +
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 campo">
                        <label for="estado_id">Estado *</label>
                        <select name="estado_id" id="estado_id" required>
                            @foreach ($estados as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8 campo">
                        <label for="serie_medida">Serie / Medida</label>
                        <input type="text" name="serie_medida" id="serie_medida" maxlength="100" class="text-uppercase" placeholder="NRO. DE SERIE O MEDIDAS">
                    </div>

                    <div class="col-12 campo">
                        <label for="otras_especificaciones">Otras Especificaciones</label>
                        <input type="text" name="otras_especificaciones" id="otras_especificaciones" maxlength="100" class="text-uppercase" placeholder="OBSERVACIONES O DETALLES ADICIONALES">
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

{{-- Mini-modal reutilizable para creación rápida de Sala, Grupo, Tipo, Marca o Modelo --}}
<div class="modal fade" id="modalCatalogoRapido" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 440px;">
        <form id="formCatalogoRapido" class="modal-content mi-modal" style="box-shadow: 0 20px 50px rgba(0,0,0,0.85);">
            <div class="modal-header">
                <div class="titulo">
                    <span class="punto"></span>
                    <h2 id="tituloCatalogoRapido">Nuevo Registro</h2>
                </div>
                <button type="button" class="cerrar" data-bs-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div id="errorCatalogoRapido" class="alert alert-danger py-2 px-3 mb-3 d-none" style="font-size: 0.85rem;"></div>

                <div class="campo mb-3">
                    <label for="nombreCatalogoRapido" id="labelCatalogoRapido">Nombre *</label>
                    <input type="text" id="nombreCatalogoRapido" name="nombre" maxlength="60" class="text-uppercase" required autocomplete="off">
                </div>

                <div class="campo d-none" id="grupoObservacionRapido">
                    <label for="observacionCatalogoRapido">Ubicación / Observación</label>
                    <input type="text" id="observacionCatalogoRapido" name="observacion" maxlength="200" placeholder="Ej. 2do piso, Pabellón B...">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-guardar" id="btnGuardarCatalogoRapido">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('js/organizacion.js') }}"></script>
    <script src="{{ asset('js/bien.js') }}"></script>
    <script src="{{ asset('js/bien-catalogo-rapido.js') }}"></script>
    <script src="{{ asset('js/bien-filtros.js') }}?v=2"></script>
@endpush