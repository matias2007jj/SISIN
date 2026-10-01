<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Plantilla Modal</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;

            background: #050B09;
        }

        /* MODAL */

        .modal {
            width: 500px;

            background: #0C1713;

            border: 1px solid #17352C;

            border-radius: 14px;

            overflow: hidden;

            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        /* CABECERA */

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 20px 24px;

            background: #0D1915;

            border-bottom: 1px solid #17352C;
        }

        .titulo {
            display: flex;
            align-items: center;

            gap: 10px;
        }

        .punto {
            width: 9px;
            height: 9px;

            background: #00D39A;

            border-radius: 50%;
        }

        .titulo h2 {
            color: #F1F5F3;

            font-size: 18px;
        }

        .cerrar {
            background: none;
            border: none;

            color: #7F8C87;

            font-size: 25px;

            cursor: pointer;
        }

        .cerrar:hover {
            color: #00D39A;
        }

        /* CUERPO */

        .modal-body {
            padding: 25px;
        }

        .campo {
            margin-bottom: 18px;
        }

        .campo label {
            display: block;

            margin-bottom: 7px;

            color: #B7C3BE;

            font-size: 14px;
        }

        .campo input,
        .campo textarea,
        .campo select {
            width: 100%;

            padding: 11px 13px;

            background: #07100D;

            border: 1px solid #1B3930;

            border-radius: 8px;

            color: #F1F5F3;

            outline: none;
        }

        .campo input:focus,
        .campo textarea:focus,
        .campo select:focus {
            border-color: #00D39A;
        }

        /* PIE */

        .modal-footer {
            display: flex;
            justify-content: flex-end;

            gap: 10px;

            padding: 16px 24px;

            background: #0A1411;

            border-top: 1px solid #17352C;
        }

        /* BOTONES */

        .btn {
            padding: 10px 18px;

            border-radius: 8px;

            cursor: pointer;

            font-size: 14px;
        }

        .btn-cancelar {
            background: #12241E;

            border: 1px solid #21463A;

            color: #B9C7C1;
        }

        .btn-guardar {
            background: #00D39A;

            border: none;

            color: #06110D;

            font-weight: bold;
        }

        .btn-guardar:hover {
            background: #00E3A5;
        }

    </style>

</head>

<body>

    <!-- PLANTILLA DEL MODAL -->

    <div class="modal">

        <!-- CABECERA -->

        <div class="modal-header">

            <div class="titulo">

                <span class="punto"></span>

                <h2>Nuevo registro</h2>

            </div>

            <button class="cerrar">
                ×
            </button>

        </div>


        <!-- CUERPO -->

        <div class="modal-body">

            <div class="campo">

                <label>
                    Nombre
                </label>

                <input
                    type="text"
                    placeholder="Ingrese el nombre"
                >

            </div>


            <div class="campo">

                <label>
                    Descripción
                </label>

                <textarea
                    rows="4"
                    placeholder="Ingrese una descripción"
                ></textarea>

            </div>

        </div>


        <!-- PIE -->

        <div class="modal-footer">

            <button class="btn btn-cancelar">
                Cancelar
            </button>

            <button class="btn btn-guardar">
                Guardar
            </button>

        </div>

    </div>

</body>

</html>