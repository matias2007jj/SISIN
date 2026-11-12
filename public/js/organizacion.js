document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Buscador de la tabla ---------- */
    var input    = document.getElementById('inputBuscar');
    var limpiar  = document.getElementById('btnLimpiarBuscar');
    var contador = document.getElementById('contadorRegistros');
    var filas    = document.querySelectorAll('#tablaRegistros tbody tr:not(#rowSinDatos)');

    function filtrar() {
        var texto = input.value.toUpperCase().trim();
        var visibles = 0;

        limpiar.classList.toggle('d-none', texto === '');

        filas.forEach(function (fila) {
            var coincide = fila.textContent.toUpperCase().indexOf(texto) > -1;
            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        if (contador) contador.textContent = visibles;
    }

    if (input) {
        input.addEventListener('keyup', filtrar);
        limpiar.addEventListener('click', function () {
            input.value = '';
            filtrar();
            input.focus();
        });
    }

    /* ---------- Doble clic en una fila para abrir su ficha ---------- */
    document.querySelectorAll('tr[data-href]').forEach(function (fila) {
        fila.addEventListener('dblclick', function (ev) {
            if (ev.target.closest('button, form, a')) return;
            window.location.href = fila.dataset.href;
        });
    });

    /* ---------- Ventana modal única (nuevo / editar) ---------- */
    var modal = document.getElementById('modalRegistro');
    if (!modal) return;

    var form   = document.getElementById('formRegistro');
    var metodo = form.querySelector('input[name="_method"]');

    modal.addEventListener('show.bs.modal', function (ev) {
        var boton = ev.relatedTarget;
        var crudo = boton && boton.dataset ? boton.dataset.registro : null;
        var datos = crudo ? JSON.parse(crudo) : null;

        form.reset();
        form.action = datos ? boton.dataset.url : form.dataset.urlNuevo;
        metodo.disabled = !datos;
        modal.querySelector('.js-titulo').textContent = datos ? form.dataset.tituloEditar : form.dataset.tituloNuevo;
        modal.querySelectorAll('.js-solo-nuevo').forEach(function (el) { el.classList.toggle('d-none', !!datos); });

        if (datos) {
            Object.keys(datos).forEach(function (clave) {
                var campo = form.elements[clave];
                if (!campo || typeof campo.type === 'undefined') return;

                if (campo.type === 'checkbox') {
                    campo.checked = !!datos[clave];
                } else {
                    campo.value = datos[clave] === null ? '' : datos[clave];
                }
            });
        }

        document.dispatchEvent(new CustomEvent('registro:abrir', { detail: datos }));
    });
});
