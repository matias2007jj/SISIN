function MostrarMensaje(ti, men, ico) {
    Swal.fire({
        position: "top-end",
        icon: ico,
        title: ti,
        text: men,
        background: "rgba(10, 20, 15, 0.98)",
        color: "#ffffff",
        showConfirmButton: false,
        timer: 1500,
        customClass: {
            popup: 'swal-dark-popup'
        }
    });
}

function MostrarAlerta(titulo, descripcion, tipoAlerta) {
    let colorIcono = '#00e676';
    if (tipoAlerta === 'error') colorIcono = '#ff4d4d';  

    Swal.fire({
        title: titulo,
        text: descripcion,
        icon: tipoAlerta,
        iconColor: colorIcono,
        background: "rgba(10, 20, 15, 0.98)",
        color: "#ffffff",
        confirmButtonColor: "#00e676",
        confirmButtonText: '<span style="color: #030705; font-weight: 800;">OK</span>',
        customClass: {
            popup: 'swal-dark-popup',
            confirmButton: 'swal-dark-button'
        }
    });
}