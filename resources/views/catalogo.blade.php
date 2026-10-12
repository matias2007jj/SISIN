@extends('layouts.sisin')

@section('title', $titulo . ' - SISIN')

@push('styles')
    <link href="{{ asset('css/catalogo.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container catalogo-page py-2">

    <section class="catalogo-panel" id="catalogo"
             data-base="{{ route('catalogo.index', $catalogo) }}"
             data-titulo="{{ $titulo }}">

        <header class="catalogo-titlebar">
            <i class="fa-solid fa-table-list"></i>
            <h2>{{ $titulo }}</h2>
            <span class="count"><span id="contador">{{ $filas->count() }}</span> registros</span>
        </header>

        @if (session('mensaje'))
            <div class="catalogo-alert ok">{{ session('mensaje') }}</div>
        @endif

        @if ($errors->any())
            <div class="catalogo-alert error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Barra de botones -->
        <div class="catalogo-toolbar" role="toolbar" aria-label="Acciones">
            <button type="button" class="tb-btn" id="btnNuevo" title="Nuevo" aria-label="Nuevo">
                <i class="fa-solid fa-file-circle-plus"></i>
            </button>
            <button type="button" class="tb-btn" id="btnEditar" title="Editar (selecciona una fila)" aria-label="Editar" disabled>
                <i class="fa-solid fa-file-pen"></i>
            </button>
            <button type="button" class="tb-btn danger" id="btnEliminar" title="Eliminar (selecciona una fila)" aria-label="Eliminar" disabled>
                <i class="fa-solid fa-circle-xmark"></i>
            </button>

            <span class="tb-sep"></span>

            <button type="button" class="tb-btn" id="btnActualizar" title="Actualizar" aria-label="Actualizar">
                <i class="fa-solid fa-rotate"></i>
            </button>
            <button type="button" class="tb-btn" id="btnReporte" title="Reporte (imprimir)" aria-label="Reporte">
                <i class="fa-solid fa-file-lines"></i>
            </button>
            <button type="button" class="tb-btn" id="btnExcel" title="Exportar a Excel" aria-label="Exportar a Excel">
                <i class="fa-solid fa-file-excel"></i>
            </button>

            <span class="tb-sep"></span>

            <a href="{{ url('/menu') }}" class="tb-btn" title="Salir" aria-label="Salir">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </div>

        <!-- Buscador -->
        <div class="catalogo-search">
            <label for="buscar">BUSCAR:</label>
            <input type="search" id="buscar" placeholder="Escribe para filtrar..." autocomplete="off">
        </div>

        <!-- Tabla -->
        <div class="catalogo-tablewrap">
            <table class="catalogo-table" id="tablaCatalogo">
                <thead>
                    <tr>
                        <th class="col-sel"></th>
                        <th>Nombre</th>
                        <th>Fecha de creación</th>
                        <th>Fecha de modificación</th>
                        <th>Creado por</th>
                        <th>Modificado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filas as $fila)
                        <tr data-id="{{ $fila->{$pk} }}"
                            data-nombre="{{ $fila->nombre }}"
                            data-descripcion="{{ $fila->descripcion ?? '' }}">
                            <td class="col-sel"><i class="fa-solid fa-caret-right"></i></td>
                            <td>{{ $fila->nombre }}</td>
                            <td>{{ !empty($fila->fechaCreacion) ? \Carbon\Carbon::parse($fila->fechaCreacion)->format('d/m/Y H:i') : '' }}</td>
                            <td>{{ !empty($fila->fechaModificacion) ? \Carbon\Carbon::parse($fila->fechaModificacion)->format('d/m/Y H:i') : '' }}</td>
                            <td>{{ $fila->idUsuarioCrea ?? '' }}</td>
                            <td>{{ $fila->idUsuarioModifica ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="vacio">Aún no hay registros en {{ $titulo }}. Usa el botón "Nuevo" para agregar uno.</td>
                        </tr>
                    @endforelse
                    <tr id="sinResultados" hidden>
                        <td colspan="6" class="vacio">No se encontraron resultados.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Modal: Nuevo / Editar registro -->
<div class="modal fade catalogo-modal" id="modalRegistro" tabindex="-1" aria-labelledby="modalTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formRegistro" class="modal-content mi-modal" method="POST" action="{{ route('catalogo.store', $catalogo) }}">
            @csrf
            <input type="hidden" name="_method" id="formMetodo" value="POST">
            <input type="hidden" name="registro_id" id="registroId" value="">

            <div class="modal-header">
                <div class="titulo">
                    <span class="punto"></span>
                    <h2 id="modalTitulo">Nuevo registro</h2>
                </div>
                <button type="button" class="cerrar" data-bs-dismiss="modal" aria-label="Cerrar">&times;</button>
            </div>

            <div class="modal-body">
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre" maxlength="100" required>
                </div>

                <div class="campo">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4" placeholder="Ingrese una descripción" maxlength="255"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-guardar">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="formEliminar" method="POST" action="" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    (function () {
        const app        = document.getElementById('catalogo');
        const baseUrl    = app.dataset.base;
        const titulo     = app.dataset.titulo;
        const tabla      = document.getElementById('tablaCatalogo');
        const filas      = Array.from(tabla.querySelectorAll('tbody tr[data-id]'));
        const buscar     = document.getElementById('buscar');
        const contador   = document.getElementById('contador');
        const sinResult  = document.getElementById('sinResultados');
        const btnEditar  = document.getElementById('btnEditar');
        const btnEliminar = document.getElementById('btnEliminar');

        const modalEl    = document.getElementById('modalRegistro');
        const modal      = new bootstrap.Modal(modalEl);
        const form       = document.getElementById('formRegistro');
        const metodo     = document.getElementById('formMetodo');
        const registroId = document.getElementById('registroId');
        const tituloModal = document.getElementById('modalTitulo');

        let seleccionada = null;

        const datosDe = tr => ({
            id: tr.dataset.id,
            nombre: tr.dataset.nombre,
            descripcion: tr.dataset.descripcion
        });

        function seleccionar(tr) {
            filas.forEach(f => f.classList.remove('is-selected'));
            seleccionada = tr;
            if (tr) tr.classList.add('is-selected');
            btnEditar.disabled = btnEliminar.disabled = !tr;
        }

        function abrirModal(modo, d = {}) {
            const editando = modo === 'editar';
            tituloModal.textContent = editando ? 'Editar registro' : 'Nuevo registro';
            form.action = editando ? baseUrl + '/' + d.id : baseUrl;
            metodo.value = editando ? 'PUT' : 'POST';
            registroId.value = editando ? d.id : '';
            form.elements['nombre'].value = d.nombre || '';
            form.elements['descripcion'].value = d.descripcion || '';
            modal.show();
        }

        modalEl.addEventListener('shown.bs.modal', () => form.elements['nombre'].focus());

        // Selección de filas (clic) y edición rápida (doble clic)
        filas.forEach(tr => {
            tr.addEventListener('click', () => seleccionar(tr));
            tr.addEventListener('dblclick', () => abrirModal('editar', datosDe(tr)));
        });

        // Botones de la barra
        document.getElementById('btnNuevo').addEventListener('click', () => abrirModal('nuevo'));
        btnEditar.addEventListener('click', () => { if (seleccionada) abrirModal('editar', datosDe(seleccionada)); });

        btnEliminar.addEventListener('click', () => {
            if (!seleccionada) return;
            if (!confirm('¿Eliminar "' + seleccionada.dataset.nombre + '"?')) return;
            const f = document.getElementById('formEliminar');
            f.action = baseUrl + '/' + seleccionada.dataset.id;
            f.submit();
        });

        document.getElementById('btnActualizar').addEventListener('click', () => window.location.reload());
        document.getElementById('btnReporte').addEventListener('click', () => window.print());

        // Exportar a Excel (CSV con separador ; y acentos correctos)
        document.getElementById('btnExcel').addEventListener('click', () => {
            const encabezados = Array.from(tabla.querySelectorAll('thead th')).slice(1).map(th => th.textContent.trim());
            const datos = filas.filter(tr => !tr.hidden)
                .map(tr => Array.from(tr.cells).slice(1).map(td => td.textContent.trim()));
            const csv = [encabezados, ...datos]
                .map(r => r.map(c => '"' + c.replace(/"/g, '""') + '"').join(';'))
                .join('\r\n');
            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = titulo.toLowerCase() + '.csv';
            a.click();
            URL.revokeObjectURL(a.href);
        });

        // Buscador en vivo
        buscar.addEventListener('input', () => {
            const q = buscar.value.trim().toLowerCase();
            let visibles = 0;
            filas.forEach(tr => {
                const coincide = tr.textContent.toLowerCase().includes(q);
                tr.hidden = !coincide;
                if (coincide) visibles++;
            });
            contador.textContent = visibles;
            sinResult.hidden = !(filas.length > 0 && visibles === 0);
            if (seleccionada && seleccionada.hidden) seleccionar(null);
        });

        // Si el servidor devolvió errores de validación, se vuelve a abrir el modal con lo escrito
        @if ($errors->has('nombre') || $errors->has('descripcion'))
            abrirModal(@json(old('registro_id') ? 'editar' : 'nuevo'), {
                id: @json(old('registro_id')),
                nombre: @json(old('nombre')),
                descripcion: @json(old('descripcion'))
            });
        @endif
    })();
</script>
@endpush