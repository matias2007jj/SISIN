<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Menú Principal - SISIN')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tipografía -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <!-- NAVBAR SUPERIOR -->
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

                    <!-- Almacén -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('catalogo.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-warehouse"></i> Almacén
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <!-- Submenú: Tablas Generales -->
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

                    <!-- Reportes -->
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

                    <!-- Usuarios -->
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

                <!-- Usuario -->
                <div class="dropdown user-menu mt-3 mt-xl-0">
                    <a class="user-chip d-flex align-items-center gap-2 text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        <span>{{ auth()->user()->name ?? 'Usuario' }}</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-logout">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
          </div>
        </div>
    </nav>

    @yield('content')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Submenús anidados (Almacén > Tablas Generales) -->
    <script>
        document.querySelectorAll('.submenu-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggle.parentElement.classList.toggle('open');
            });
        });

        document.querySelectorAll('.navbar-custom .dropdown').forEach(function (dd) {
            dd.addEventListener('hidden.bs.dropdown', function () {
                dd.querySelectorAll('.dropdown-submenu.open').forEach(function (s) {
                    s.classList.remove('open');
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>