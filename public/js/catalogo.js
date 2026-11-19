document.addEventListener('DOMContentLoaded', function () {
    const inputBuscar = document.getElementById('inputBuscar');
    const btnLimpiar = document.getElementById('btnLimpiarBuscar');

    if (inputBuscar) {
        const tablaSelector = inputBuscar.dataset.tabla || 'table tbody tr:not(#rowSinDatos)';
        const contadorSelector = inputBuscar.dataset.contador || null;

        const rows = document.querySelectorAll(tablaSelector);
        const contador = contadorSelector ? document.querySelector(contadorSelector) : null;

        function filtrarFilas() {
            const filter = inputBuscar.value.toUpperCase().trim();
            let visibles = 0;

            if (btnLimpiar) {
                btnLimpiar.classList.toggle('d-none', filter === '');
            }

            rows.forEach((row) => {
                const textContent = row.textContent || row.innerText;
                const coincide = textContent.toUpperCase().indexOf(filter) > -1;

                row.style.display = coincide ? '' : 'none';
                if (coincide) {
                    visibles++;
                }
            });

            if (contador) {
                contador.textContent = visibles;
            }
        }

        inputBuscar.addEventListener('keyup', filtrarFilas);

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function () {
                inputBuscar.value = '';
                filtrarFilas();
                inputBuscar.focus();
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