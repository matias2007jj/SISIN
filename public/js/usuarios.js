document.addEventListener('DOMContentLoaded', function () {
    var modal           = document.getElementById('modalRegistro');
    var rol             = document.getElementById('tipo_usuario');
    var username        = document.getElementById('username');
    var password        = document.getElementById('password');
    var apellidos       = document.getElementById('apellidos');
    var nombres         = document.getElementById('nombres');
    var dni             = document.getElementById('dni');
    var celular         = document.getElementById('celular');
    var bloquePersonal  = modal ? modal.querySelector('.js-bloque-personal') : null;
    var bloqueProgramas = modal ? modal.querySelector('.js-bloque-programas') : null;
    var reqPersonal     = modal ? modal.querySelectorAll('.js-req-personal') : [];
    if (!modal) return;

    function soloLetras(input) {
        if (!input) return;
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ' -]/g, '');
            this.setCustomValidity('');
        });
    }

    function soloDigitosExactos(input, longitud, etiqueta) {
        if (!input) return;
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, longitud);
            if (this.value.length > 0 && this.value.length < longitud) {
                this.setCustomValidity(etiqueta + ' debe tener exactamente ' + longitud + ' dígitos numéricos.');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    soloLetras(apellidos);
    soloLetras(nombres);
    soloDigitosExactos(dni, 8, 'El DNI');
    soloDigitosExactos(celular, 9, 'El número de celular');

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

        [apellidos, nombres, dni, celular].forEach(function (el) {
            if (el) el.setCustomValidity('');
        });

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