document.addEventListener('DOMContentLoaded', function () {
    const inputBuscar = document.getElementById('inputBuscar');
    const btnLimpiar = document.getElementById('btnLimpiarBuscar');
    const filtroInstitucion = document.getElementById('filtroInstitucion');
    const filtroPrograma = document.getElementById('filtroPrograma');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');

    if (inputBuscar || filtroInstitucion || filtroPrograma) {
        const tablaSelector = inputBuscar?.dataset.tabla || 'table tbody tr:not(#rowSinDatos)';
        const contadorSelector = inputBuscar?.dataset.contador || null;

        const rows = document.querySelectorAll(tablaSelector);
        const contador = contadorSelector ? document.querySelector(contadorSelector) : null;

        // Guardar opciones originales de programas para filtrar en cascada por institución
        const opcionesPrograma = filtroPrograma
            ? Array.from(filtroPrograma.options).slice(1).map((opt) => ({
                  value: opt.value,
                  text: opt.text,
                  institucionId: opt.dataset.institucionId || '',
              }))
            : [];

        function actualizarComboProgramas() {
            if (!filtroInstitucion || !filtroPrograma) return;

            const instId = filtroInstitucion.value;
            const progActual = filtroPrograma.value;

            filtroPrograma.innerHTML = '<option value="">TODOS LOS PROGRAMAS</option>';

            opcionesPrograma.forEach((op) => {
                if (!instId || op.institucionId === instId) {
                    const option = document.createElement('option');
                    option.value = op.value;
                    option.textContent = op.text;
                    option.dataset.institucionId = op.institucionId;
                    if (op.value === progActual) {
                        option.selected = true;
                    }
                    filtroPrograma.appendChild(option);
                }
            });
        }

        function actualizarUrlsExportacion(texto, instId, progId) {
            document.querySelectorAll('.js-btn-exportar').forEach((btn) => {
                const baseUrl = btn.dataset.baseUrl;
                if (!baseUrl) return;

                const url = new URL(baseUrl, window.location.origin);
                if (texto) url.searchParams.set('buscar', texto);
                if (instId) url.searchParams.set('institucion_id', instId);
                if (progId) url.searchParams.set('programa_estudio_id', progId);

                btn.href = url.toString();
            });
        }

        function filtrarFilas() {
            const filter = inputBuscar ? inputBuscar.value.toUpperCase().trim() : '';
            const instId = filtroInstitucion ? filtroInstitucion.value : '';
            const progId = filtroPrograma ? filtroPrograma.value : '';
            let visibles = 0;

            if (btnLimpiar) {
                btnLimpiar.classList.toggle('d-none', filter === '');
            }

            if (btnLimpiarFiltros) {
                btnLimpiarFiltros.classList.toggle('d-none', !filter && !instId && !progId);
            }

            rows.forEach((row) => {
                const textContent = (row.textContent || row.innerText).toUpperCase();
                const rowInstId = row.dataset.institucionId || '';
                const rowProgId = row.dataset.programaId || '';

                const coincideTexto = !filter || textContent.indexOf(filter) > -1;
                const coincideInst = !instId || rowInstId === instId;
                const coincideProg = !progId || rowProgId === progId;

                const coincide = coincideTexto && coincideInst && coincideProg;
                row.style.display = coincide ? '' : 'none';
                if (coincide) {
                    visibles++;
                }
            });

            if (contador) {
                contador.textContent = visibles;
            }

            actualizarUrlsExportacion(filter, instId, progId);
        }

        if (inputBuscar) {
            inputBuscar.addEventListener('keyup', filtrarFilas);
        }

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function () {
                inputBuscar.value = '';
                filtrarFilas();
                inputBuscar.focus();
            });
        }

        if (filtroInstitucion) {
            filtroInstitucion.addEventListener('change', function () {
                actualizarComboProgramas();
                filtrarFilas();
            });
        }

        if (filtroPrograma) {
            filtroPrograma.addEventListener('change', filtrarFilas);
        }

        if (btnLimpiarFiltros) {
            btnLimpiarFiltros.addEventListener('click', function () {
                if (inputBuscar) inputBuscar.value = '';
                if (filtroInstitucion) {
                    filtroInstitucion.value = '';
                    actualizarComboProgramas();
                }
                if (filtroPrograma) filtroPrograma.value = '';
                filtrarFilas();
            });
        }
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', function (e) {
            const mensaje = form.dataset.confirm || '¿Está seguro de eliminar este registro?';
            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
});