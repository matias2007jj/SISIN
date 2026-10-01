<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Registro - SISIN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">
</head>

<body>

    <div class="mi-modal">

        <div class="modal-header">
            <div class="titulo">
                <span class="punto"></span>
                <h2>Nuevo registro</h2>
            </div>
            <button type="button" class="cerrar">&times;</button>
        </div>

        <div class="modal-body">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre">
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" placeholder="Ingrese una descripción"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-cancelar">Cancelar</button>
            <button type="button" class="btn btn-guardar">Guardar</button>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/modal.js') }}"></script>

</body>

</html>