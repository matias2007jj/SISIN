<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Sisventas | Sistema de inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="../assets/images/logo-p.png" />

    <!-- Bootstrap Css -->
    <link href="../assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="../assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
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
                                            <div>
                                                <a href="index.html" class="">
                                                    <img src="../assets/images/LOGODSI2.png" alt=""
                                                        height="200" class="auth-logo logo-dark mx-auto" />
                                                </a>
                                            </div>

                                            <h4 class="font-size-18 mt-4">
                                                Bienvenidos al Sistema de Control de Ventas
                                            </h4>
                                            <p class="text-muted">Inicie Sesión para Ingresar.</p>
                                        </div>
                                        <div class="p-2 mt-5">
                                            <div class="auth-form-group-custom mb-4">
                                                <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                <label for="username" class="fw-semibold">Usuario</label>
                                                <input type="text" class="form-control" id="usu"
                                                    placeholder="Ingrese Usuario"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase()" />
                                            </div>

                                            <div class="auth-form-group-custom mb-4">
                                                <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                <label for="userpassword">Password</label>
                                                <input type="password" class="form-control" id="pass"
                                                    placeholder="Ingrese Password"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase()" />
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

    <!-- JAVASCRIPT -->
    <script src="../assets/libs/jquery/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../assets/libs/node-waves/waves.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/mensaje.js') }}"></script>
    <script src="{{asset('js/acceso.js')}}"></script>

</body>

</html>
