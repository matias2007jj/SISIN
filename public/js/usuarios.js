document.addEventListener('DOMContentLoaded', function () {
    var modal    = document.getElementById('modalRegistro');
    var selector = document.getElementById('personal_id');
    var rol      = document.getElementById('tipo_usuario');
    var username = document.getElementById('username');
    if (!modal) return;

    document.addEventListener('registro:abrir', function (ev) {
        var datos = ev.detail;

        modal.querySelectorAll('input[name="programas[]"]').forEach(function (c) {
            c.checked = !!(datos && datos.programas && datos.programas.indexOf(parseInt(c.value, 10)) > -1);
        });
        modal.querySelectorAll('.js-solo-editar').forEach(function (el) { el.classList.toggle('d-none', !datos); });

        if (username) username.placeholder = datos ? 'Nombre de usuario (puede ser un correo)' : 'Vacío = DNI';

        var titulo = modal.querySelector('.js-persona');
        if (titulo) titulo.textContent = datos ? datos.persona : '';
    });

    if (selector) {
        selector.addEventListener('change', function () {
            var op = selector.options[selector.selectedIndex];
            if (!op || !op.value) return;
            if (op.dataset.rol) rol.value = op.dataset.rol;

            if (op.dataset.programas) {
                var ids = JSON.parse(op.dataset.programas);
                modal.querySelectorAll('input[name="programas[]"]').forEach(function (c) {
                    c.checked = ids.indexOf(parseInt(c.value, 10)) > -1;
                });
            }
            username.placeholder = op.dataset.dni ? 'Vacío = ' + op.dataset.dni : 'Vacío = DNI';
        });
    }
});
