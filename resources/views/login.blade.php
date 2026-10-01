<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Sisventas | Sistema de inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <!-- Bootstrap CSS (CDN para asegurar que cargue correctamente) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Remind / Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Tus estilos personalizados -->
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="auth-body-bg">
    <div>
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-lg-4">
                    <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                        <div class="w-100">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div>
                                        <div class="text-center">
                                            <h4 class="font-size-18 mt-4">
                                                Bienvenidos al Sistema de Control de Ventas
                                            </h4>
                                            <p class="text-muted">Inicie Sesión para Ingresar.</p>
                                        </div>
                                        <div class="p-2 mt-5">
                                            <div class="auth-form-group-custom mb-4">
                                                <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                <label for="usu" class="fw-semibold">Usuario</label>
                                                <input type="text" class="form-control" id="usu"
                                                    placeholder="Ingrese Usuario"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase()" />
                                            </div>

                                            <div class="auth-form-group-custom mb-4">
                                                <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                <label for="pass">Password</label>
                                                <input type="password" class="form-control" id="pass"
                                                    placeholder="Ingrese Password" />
                                            </div>

                                            <div class="mt-4 text-center">
                                                <button class="btn btn-primary w-md waves-effect waves-light"
                                                    id="btn_acceso">ACCEDER</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="authentication-bg">
                        <div class="bg-overlay"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT (jQuery por CDN para evitar errores 404) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tus scripts locales ubicados en public/js/ -->
    <script src="{{ asset('js/mensaje.js') }}"></script>
    <script src="{{ asset('js/acceso.js') }}"></script>

</body>

</html>