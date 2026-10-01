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