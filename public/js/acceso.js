$(document).ready(function() {
    $('#btn_acceso').on('click', function(e) {
        e.preventDefault();

        let usu = $('#usu').val();
        let pass = $('#pass').val();

        if (usu === "" || pass === "") {
            Swal.fire('Atención', 'Complete todos los campos', 'warning');
            return;
        }

        $.ajax({
            url: '/login',
            type: 'POST',
            data: {
                usu: usu,
                pass: pass,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: response.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "/menu";
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Usuario o contraseña incorrectos', 'error');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const btnAcceso = document.getElementById('btn_acceso');
    const togglePass = document.getElementById('togglePass');

    if (loginForm && btnAcceso) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            btnAcceso.click();
        });
    }

    if (togglePass) {
        togglePass.addEventListener('click', function () {
            const input = document.getElementById('pass');
            const eyeOpen = document.getElementById('iconEyeOpen');
            const eyeClosed = document.getElementById('iconEyeClosed');
            if (!input || !eyeOpen || !eyeClosed) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            eyeOpen.classList.toggle('d-none', isPassword);
            eyeClosed.classList.toggle('d-none', !isPassword);
        });
    }
});