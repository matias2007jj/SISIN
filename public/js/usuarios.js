document.addEventListener('DOMContentLoaded', function () {
    var modal           = document.getElementById('modalRegistro');
    var rol             = document.getElementById('tipo_usuario');
    var username        = document.getElementById('username');
    var password        = document.getElementById('password');
    var bloquePersonal  = modal ? modal.querySelector('.js-bloque-personal') : null;
    var bloqueProgramas = modal ? modal.querySelector('.js-bloque-programas') : null;
    var reqPersonal     = modal ? modal.querySelectorAll('.js-req-personal') : [];
    if (!modal) return;

    function actualizarBloquesPorRol() {
        var esAdminInst = rol && rol.value === 'ADMIN_INSTITUCION';

        if (bloquePersonal) bloquePersonal.classList.toggle('d-none', esAdminInst);
        if (bloqueProgramas) bloqueProgramas.classList.toggle('d-none', esAdminInst);

        reqPersonal.forEach(function (input) {
            input.required = !esAdminInst;
        });
    }

    document.addEventListener('registro:abrir', function (ev) {
        var datos = ev.detail;

        modal.querySelectorAll('input[name="programas[]"]').forEach(function (c) {
            c.checked = !!(datos && datos.programas && datos.programas.indexOf(parseInt(c.value, 10)) > -1);
        });
        modal.querySelectorAll('.js-solo-editar').forEach(function (el) {
            el.classList.toggle('d-none', !datos);
        });

        if (username) {
            username.placeholder = datos ? 'Nombre de usuario' : 'Vacío = DNI';
        }
        if (password) {
            password.placeholder = datos ? 'Vacío = mantener la actual' : 'Vacío = generar automática';
        }

        actualizarBloquesPorRol();
    });

    if (rol) {
        rol.addEventListener('change', actualizarBloquesPorRol);
    }
});