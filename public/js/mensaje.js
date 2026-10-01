function MostrarMensaje(ti, men, ico) {
    Swal.fire({
        position: "top-end",
        icon: ico,
        title: ti,
        text: men,
        iconColor: "steelBlue",
        showConfirmButton: false,
        timer: 1500,
    });
}
function MostrarAlerta(titulo, descripcion, tipoAlerta) {
  Swal.fire({
    title: titulo,
    text: descripcion,
    icon: tipoAlerta,
    iconColor: "#6592ff",
    confirmButtonColor: "#3085d6",
  });
}
