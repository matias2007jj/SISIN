<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - SISIN</title>
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
</head>
<body>

@php
    // Resumen: pásalo desde el controlador con view('menu', compact('stats')).
    // Si no lo pasas, se muestran ceros.
    $stats = $stats ?? [];
    $summary = [
        ['label' => 'Productos',        'value' => $stats['productos'] ?? 0,  'icon' => 'fa-cart-shopping'],
        ['label' => 'Clientes',         'value' => $stats['clientes'] ?? 0,   'icon' => 'fa-user-group'],
        ['label' => 'Facturas del mes', 'value' => $stats['facturas'] ?? 0,   'icon' => 'fa-receipt'],
        ['label' => 'Con stock bajo',   'value' => $stats['stock_bajo'] ?? 0, 'icon' => 'fa-triangle-exclamation', 'warn' => true],
    ];
@endphp

    <!-- NAVBAR SUPERIOR -->
    <nav class="navbar navbar-expand-xl navbar-custom mb-4">
        <div class="container">
          <div class="nav-pill d-flex flex-wrap align-items-center justify-content-between">
            <a class="navbar-brand fw-bold m-0" href="#">
                <span class="brand-mark"><i class="fa-solid fa-boxes-stacked"></i></span>
                Sistema SISIN
            </a>

            <button class="btn btn-toggle-menu d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#topMenu" aria-controls="topMenu" aria-expanded="false" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="topMenu">
                <ul class="navbar-nav top-nav me-auto ms-xl-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('menu') ? 'active' : '' }}" href="#" @if(request()->is('menu')) aria-current="page" @endif>
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-user-group"></i> Clientes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-cart-shopping"></i> Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-receipt"></i> Facturas</a></li>

                    <li class="nav-divider d-none d-xl-block" aria-hidden="true"></li>

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
                        @if (Route::has('logout'))
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        @else
                            <li><span class="dropdown-item-text text-muted">Sesión activa</span></li>
                        @endif
                    </ul>
                </div>
            </div>
          </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container py-3">
        <div class="page-head mb-4">
            <h2>Panel de Control</h2>
            <p>Resumen del inventario y accesos a los módulos del sistema.</p>
        </div>

        <!-- Resumen -->
        <section class="summary mb-4" aria-label="Resumen">
            @foreach ($summary as $s)
                <div class="summary-item {{ !empty($s['warn']) && $s['value'] > 0 ? 'is-warn' : '' }}">
                    <i class="fa-solid {{ $s['icon'] }}"></i>
                    <div>
                        <strong>{{ number_format($s['value']) }}</strong>
                        <span>{{ $s['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Módulos -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card menu-card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                        <span class="module-icon"><i class="fa-solid fa-database"></i></span>
                        <h5 class="card-title fw-semibold mb-1">Módulo 1</h5>
                        <p class="card-text mb-4 flex-grow-1">Gestión y registro de datos.</p>
                        <a href="#" class="btn btn-green w-100">Ingresar</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card menu-card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                        <span class="module-icon"><i class="fa-solid fa-chart-column"></i></span>
                        <h5 class="card-title fw-semibold mb-1">Reportes</h5>
                        <p class="card-text mb-4 flex-grow-1">Consulta y exportación de reportes.</p>
                        <a href="#" class="btn btn-green w-100">Ver reportes</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card menu-card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                        <span class="module-icon"><i class="fa-solid fa-gear"></i></span>
                        <h5 class="card-title fw-semibold mb-1">Configuración</h5>
                        <p class="card-text mb-4 flex-grow-1">Ajustes generales del sistema.</p>
                        <a href="#" class="btn btn-secondary-custom w-100">Configurar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>