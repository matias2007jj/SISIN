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
    
    <style>
        :root {
            --bg-body: #030705;
            --bg-card: rgba(10, 20, 15, 0.95);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --accent-color: #00e676;
            --border-glow: rgba(0, 230, 118, 0.4);
        }

        html, body {
            height: 100%;
            margin: 0;
            background-color: var(--bg-body) !important;
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body.auth-body-bg {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative;
            overflow: hidden;
        }

        /* Resplandor verde de fondo */
        body.auth-body-bg::before {
            content: '';
            position: absolute;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(0, 230, 118, 0.22) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
        }

        /* Tarjeta contenedora principal */
        .login-card-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 40px 35px;
            background: var(--bg-card) !important;
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(0, 230, 118, 0.2), 0 25px 50px rgba(0, 0, 0, 0.9);
            margin: 20px;
        }

        .login-card-container h4 {
            color: var(--text-main) !important;
            font-weight: 700;
            font-size: 1.3rem;
            text-shadow: 0 0 10px rgba(0, 230, 118, 0.3);
        }

        .auth-form-group-custom {
            position: relative;
            margin-bottom: 22px;
            text-align: left;
        }

        .auth-form-group-custom label {
            display: block;
            color: var(--text-main) !important;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .auth-form-group-custom .form-control {
            width: 100% !important;
            background-color: rgba(4, 10, 7, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 12px 16px 12px 40px !important;
            height: 48px !important;
            box-sizing: border-box !important;
        }

        .auth-form-group-custom .auti-custom-input-icon {
            position: absolute;
            left: 12px;
            top: 38px;
            color: var(--accent-color);
            font-size: 1.1rem;
            z-index: 5;
        }

        .auth-form-group-custom .form-control:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 15px rgba(0, 230, 118, 0.5) !important;
            outline: none;
        }

        .btn-primary-custom {
            width: 100% !important;
            background-color: var(--accent-color) !important;
            border: none !important;
            color: #030705 !important;
            font-weight: 800;
            padding: 12px !important;
            border-radius: 10px !important;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 20px rgba(0, 230, 118, 0.35);
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background-color: #00ff80 !important;
            box-shadow: 0 0 25px rgba(0, 230, 118, 0.7);
            transform: translateY(-2px);
        }
    </style>
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