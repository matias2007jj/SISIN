document.addEventListener('DOMContentLoaded', function () {
    const selPrograma = document.getElementById('filtroProgramaBien');
    const selSala = document.getElementById('filtroSalaBien');
    const inputBuscar = document.getElementById('inputBuscar');
    const btnLimpiarBuscar = document.getElementById('btnLimpiarBuscar');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltrosBien');
    const contadorEl = document.getElementById('contadorRegistros');
    const tbody = document.querySelector('#tablaRegistros tbody');

    if (!tbody) return;

    const headers = Array.from(document.querySelectorAll('#tablaRegistros thead th')).map((th) =>
        th.textContent.trim().toUpperCase()
    );
    const idxPrograma = headers.indexOf('PROGRAMA');
    const idxSala = headers.indexOf('SALA');

    const opcionesSalasOriginales = selSala
        ? Array.from(selSala.querySelectorAll('option'))
              .filter((o) => o.value !== '')
              .map((opt) => ({
                  value: opt.value,
                  text: opt.textContent.trim(),
                  programaId: opt.dataset.programaId || '',
              }))
        : [];

    function actualizarComboSalas() {
        if (!selSala) return;
        const programaId = selPrograma ? selPrograma.value : '';
        const salaActual = selSala.value;

        selSala.innerHTML = '<option value="">TODAS LAS SALAS</option>';

        const filtradas = programaId
            ? opcionesSalasOriginales.filter((s) => String(s.programaId) === String(programaId))
            : opcionesSalasOriginales;

        filtradas.forEach((s) => {
            const opt = document.createElement('option');
            opt.value = s.value;
            opt.textContent = s.text;
            opt.dataset.programaId = s.programaId;
            selSala.appendChild(opt);
        });

        if (filtradas.some((s) => String(s.value) === String(salaActual))) {
            selSala.value = salaActual;
        } else {
            selSala.value = '';
        }
    }

    function aplicarFiltrosBienes() {
        const programaId = selPrograma ? selPrograma.value : '';
        const programaNombre =
            selPrograma && selPrograma.selectedIndex > 0
                ? selPrograma.options[selPrograma.selectedIndex].textContent.trim().toUpperCase()
                : '';

        const salaId = selSala ? selSala.value : '';
        const salaNombre =
            selSala && selSala.selectedIndex > 0
                ? selSala.options[selSala.selectedIndex].textContent.trim().toUpperCase()
                : '';

        const texto = (inputBuscar ? inputBuscar.value : '').trim().toUpperCase();

        const filas = Array.from(tbody.querySelectorAll('tr:not(#rowSinDatos):not(#rowSinCoincidencias)'));
        let visibles = 0;

        filas.forEach((tr) => {
            const celdas = tr.querySelectorAll('td');
            const rowProgId = tr.dataset.programaId || '';
            const rowSalaId = tr.dataset.salaId || '';

            const celdaProgTexto =
                idxPrograma >= 0 && celdas[idxPrograma]
                    ? celdas[idxPrograma].textContent.trim().toUpperCase()
                    : '';
            const celdaSalaTexto =
                idxSala >= 0 && celdas[idxSala]
                    ? celdas[idxSala].textContent.trim().toUpperCase()
                    : '';

            const okPrograma =
                !programaId ||
                (rowProgId !== '' && String(rowProgId) === String(programaId)) ||
                celdaProgTexto === programaNombre;

            const okSala =
                !salaId ||
                (rowSalaId !== '' && String(rowSalaId) === String(salaId)) ||
                celdaSalaTexto === salaNombre;

            const okTexto = !texto || tr.textContent.toUpperCase().includes(texto);

            if (okPrograma && okSala) {
                tr.classList.remove('fila-oculta-filtro');
            } else {
                tr.classList.add('fila-oculta-filtro');
            }

            if (okPrograma && okSala && okTexto) {
                tr.style.display = '';
                visibles++;
            } else {
                tr.style.display = 'none';
            }
        });

        if (contadorEl) {
            contadorEl.textContent = visibles;
        }

        let rowVacia = document.getElementById('rowSinCoincidencias');
        if (filas.length > 0 && visibles === 0) {
            if (!rowVacia) {
                const cols = headers.length || 10;
                rowVacia = document.createElement('tr');
                rowVacia.id = 'rowSinCoincidencias';
                rowVacia.innerHTML = `<td colspan="${cols}" class="text-center py-4 text-muted">No se encontraron bienes con los filtros aplicados.</td>`;
                tbody.appendChild(rowVacia);
            }
            rowVacia.style.display = '';
        } else if (rowVacia) {
            rowVacia.style.display = 'none';
        }

        if (btnLimpiarFiltros) {
            btnLimpiarFiltros.classList.toggle('d-none', !(programaId || salaId));
        }
    }

    if (selPrograma) {
        selPrograma.addEventListener('change', function () {
            actualizarComboSalas();
            aplicarFiltrosBienes();
        });
    }

    if (selSala) {
        selSala.addEventListener('change', aplicarFiltrosBienes);
    }

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function () {
            if (btnLimpiarBuscar) {
                btnLimpiarBuscar.classList.toggle('d-none', !inputBuscar.value.trim());
            }
            aplicarFiltrosBienes();
        });
    }

    if (btnLimpiarBuscar) {
        btnLimpiarBuscar.addEventListener('click', function () {
            if (inputBuscar) inputBuscar.value = '';
            btnLimpiarBuscar.classList.add('d-none');
            aplicarFiltrosBienes();
        });
    }

    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function () {
            if (selPrograma) selPrograma.value = '';
            actualizarComboSalas();
            if (selSala) selSala.value = '';
            aplicarFiltrosBienes();
        });
    }
});