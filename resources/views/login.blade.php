<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Sisventas | Sistema de inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <!-- Bootstrap CSS y Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Hoja de estilos vinculada -->
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="auth-body-bg">

    <div class="login-card-container">
        <div class="text-center mb-4">
            <h4 class="mb-2">Bienvenidos al Sistema de Control de Ventas</h4>
            <p class="text-muted small">Inicie Sesión para Ingresar.</p>
        </div>

        <form onsubmit="return false;">
            <div class="auth-form-group-custom">
                <i class="ri-user-2-line auti-custom-input-icon"></i>
                <label for="usu">Usuario</label>
                <input type="text" class="form-control" id="usu"
                    placeholder="Ingrese Usuario"
                    onkeyup="javascript:this.value=this.value.toUpperCase()" />
            </div>

            <div class="auth-form-group-custom">
                <i class="ri-lock-2-line auti-custom-input-icon"></i>
                <label for="pass">Password</label>
                <input type="password" class="form-control" id="pass"
                    placeholder="Ingrese Password" />
            </div>

            <div class="mt-4 text-center">
                <button class="btn btn-primary-custom" id="btn_acceso" type="button">ACCEDER</button>
            </div>
        </form>
    </div>

    <!-- JAVASCRIPT -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="{{ asset('js/mensaje.js') }}"></script>
    <script src="{{ asset('js/acceso.js') }}"></script>

</body>

</html>