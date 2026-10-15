document.addEventListener('DOMContentLoaded', function () {
    const inputBuscar = document.getElementById('inputBuscar');
    const btnLimpiar = document.getElementById('btnLimpiarBuscar');
    const rows = document.querySelectorAll('#tablaTipos tbody tr:not(#rowSinDatos)');
    const contador = document.getElementById('contadorTipos');

    if (!inputBuscar) return;

    inputBuscar.addEventListener('keyup', function () {
        const filter = this.value.toUpperCase().trim();
        let visibles = 0;

        btnLimpiar.classList.toggle('d-none', filter === '');

        rows.forEach(function (row) {
            const texto = (row.textContent || row.innerText).toUpperCase();
            const coincide = texto.indexOf(filter) > -1;

            row.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        if (contador) contador.textContent = visibles;
    });

    btnLimpiar.addEventListener('click', function () {
        inputBuscar.value = '';
        inputBuscar.dispatchEvent(new Event('keyup'));
        inputBuscar.focus();
    });
});