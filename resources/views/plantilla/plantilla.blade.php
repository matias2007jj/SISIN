@if(auth()->check() || (session('usuario') && session('id_perfil')))
@php
    $nombreUsuario = auth()->user()->name ?? session('usuario') ?? 'Usuario';
    $nombreUsuario = is_scalar($nombreUsuario) ? (string) $nombreUsuario : 'Usuario';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISIN')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plantilla.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-xl navbar-custom mb-4">
        <div class="container">
          <div class="nav-pill d-flex flex-wrap align-items-center justify-content-between">
            <a class="navbar-brand fw-bold m-0" href="{{ url('/menu') }}">
                <span class="brand-mark"><i class="fa-solid fa-boxes-stacked"></i></span>
                Sistema SISIN
            </a>

            <button class="btn btn-toggle-menu d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#topMenu" aria-controls="topMenu" aria-expanded="false" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="topMenu">
                <ul class="navbar-nav top-nav me-auto ms-xl-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('menu') ? 'active' : '' }}" href="{{ url('/menu') }}" @if(request()->is('menu')) aria-current="page" @endif>
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-user-group"></i> Clientes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-cart-shopping"></i> Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-receipt"></i> Facturas</a></li>

                    <li class="nav-divider d-none d-xl-block" aria-hidden="true"></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('catalogo.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-warehouse"></i> Almacén
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li class="dropdown-submenu">
                                <a class="dropdown-item submenu-toggle" href="#">
                                    Tablas Generales <i class="fa-solid fa-chevron-right submenu-arrow"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-custom submenu">
                                    @foreach (['grupo' => 'Grupo', 'estado' => 'Estado', 'marca' => 'Marca', 'modelo' => 'Modelo', 'sala' => 'Sala', 'tipo' => 'Tipo'] as $slug => $nombre)
                                        <li>
                                            <a class="dropdown-item {{ request()->route('catalogo') === $slug ? 'active' : '' }}" href="{{ route('catalogo.index', $slug) }}">{{ $nombre }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Bienes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Toma de Inventario</a></li>
                            <li><a class="dropdown-item" href="#">Reporte Bien</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-file-lines"></i> Reportes
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li><h6 class="dropdown-header">Generales</h6></li>
                            <li><a class="dropdown-item" href="#">Reporte Ventas</a></li>
                            <li><a class="dropdown-item" href="#">Reporte Inventario</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Específicos</h6></li>
                            <li><a class="dropdown-item" href="#">Por Cliente</a></li>
                            <li><a class="dropdown-item" href="#">Por Producto</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-user"></i> Usuarios
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li><h6 class="dropdown-header">Mantenimiento</h6></li>
                            <li><a class="dropdown-item" href="#">Gestionar Usuarios</a></li>
                            <li><a class="dropdown-item" href="#">Perfiles y Permisos</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="dropdown user-menu mt-3 mt-xl-0">
                    <a class="user-chip d-flex align-items-center gap-2 text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar">{{ mb_strtoupper(mb_substr($nombreUsuario, 0, 1)) }}</span>
                        <span>{{ $nombreUsuario }}</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                        <li>
                            <a class="dropdown-item dropdown-item-logout" id="btn_salir" href="{{ route('salir.index') }}">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
          </div>
        </div>
    </nav>

    @hasSection('titulo')
        <main class="container plantilla-page pb-5">
            <div class="plantilla-card">
                <header class="plantilla-header">
                    <h2><i class="fa-solid @yield('icono', 'fa-table-list')"></i> @yield('titulo')</h2>

                    @hasSection('boton')
                        <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#@yield('modal_id', 'modalAgregar')">
                            <i class="fa-solid fa-plus me-2"></i>@yield('boton')
                        </button>
                    @endif
                </header>

                <div class="plantilla-body">
                    @yield('contenido')
                </div>
            </div>
        </main>
    @else

        <main>
            @yield('contenido')
        </main>
        
    @endif

    @yield('modales')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.urlSalir = "{{ route('salir.index') }}";
    </script>
    <script src="{{ asset('js/plantilla.js') }}"></script>
    <script src="{{ asset('js/salir.js') }}"></script>
    <script src="{{ asset('js/mensaje.js') }}"></script>

    @stack('scripts')
</body>
</html>
@else
    <script>
        alert("Usted debe loguearse para ingresar al sistema");
        window.location.href = "{{ route('acceso.index') }}";
    </script>
@endif