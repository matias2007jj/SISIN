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
    <!-- Estilos personalizados -->
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-custom px-4 py-3 mb-5">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a class="navbar-brand fw-bold m-0" href="#">
                <span>•</span> Sistema SISIN
            </a>
        </div>
        <span class="badge-title">Menú Principal</span>
    </nav>

    <!-- MENÚ LATERAL DESPLEGABLE (OFFCANVAS) -->
    <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">
                <span style="color: var(--accent-green);">•</span> Sistema SISIN
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="sidebar-nav">
                
                <!-- SECCIÓN: MENU -->
                <div class="sidebar-category">MENU</div>
                <a class="nav-link active" href="#"><i class="fa-solid fa-house"></i> Home</a>
                <a class="nav-link" href="#"><i class="fa-solid fa-user-group"></i> Clientes</a>
                <a class="nav-link" href="#"><i class="fa-solid fa-cart-shopping"></i> Productos</a>
                <a class="nav-link" href="#"><i class="fa-solid fa-tags"></i> Categorías</a>
                <a class="nav-link" href="#"><i class="fa-solid fa-receipt"></i> Facturas</a>

                <!-- SECCIÓN: REPORTES -->
                <div class="sidebar-category mt-4">REPORTES</div>
                
                <!-- Submenú Generales -->
                <div class="nav-item-dropdown">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuGenerales" role="button" aria-expanded="false">
                        <span><i class="fa-solid fa-file-lines"></i> Generales</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <div class="collapse submenu" id="menuGenerales">
                        <a href="#" class="submenu-link">Reporte Ventas</a>
                        <a href="#" class="submenu-link">Reporte Inventario</a>
                    </div>
                </div>

                <!-- Submenú Específicos -->
                <div class="nav-item-dropdown">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuEspecificos" role="button" aria-expanded="false">
                        <span><i class="fa-solid fa-file-lines"></i> Específicos</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <div class="collapse submenu" id="menuEspecificos">
                        <a href="#" class="submenu-link">Por Cliente</a>
                        <a href="#" class="submenu-link">Por Producto</a>
                    </div>
                </div>

                <!-- SECCIÓN: USUARIOS -->
                <div class="sidebar-category mt-4">USUARIOS</div>
                
                <!-- Submenú Mantenimiento -->
                <div class="nav-item-dropdown">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuMantenimiento" role="button" aria-expanded="false">
                        <span><i class="fa-regular fa-user"></i> Mantenimiento</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <div class="collapse submenu" id="menuMantenimiento">
                        <a href="#" class="submenu-link">Gestionar Usuarios</a>
                        <a href="#" class="submenu-link">Perfiles y Permisos</a>
                    </div>
                </div>

            </nav>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container py-3">
        <h2 class="mb-5 text-center fw-bold" style="letter-spacing: -0.5px;">Panel de Control</h2>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card menu-card h-100 shadow-sm text-center p-4">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-semibold mb-2">Módulo 1</h5>
                            <p class="card-text mb-4">Gestión y registro de datos.</p>
                        </div>
                        <a href="#" class="btn btn-green w-100">Ingresar</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card menu-card h-100 shadow-sm text-center p-4">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-semibold mb-2">Reportes</h5>
                            <p class="card-text mb-4">Consulta y exportación de reportes.</p>
                        </div>
                        <a href="#" class="btn btn-green w-100">Ver reportes</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card menu-card h-100 shadow-sm text-center p-4">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-semibold mb-2">Configuración</h5>
                            <p class="card-text mb-4">Ajustes generales del sistema.</p>
                        </div>
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