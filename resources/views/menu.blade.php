<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - SISIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <style>
    </style>
</head>
<body>
    <nav class="navbar navbar-custom px-4 py-3 mb-5">
        <a class="navbar-brand fw-bold" href="#">
            <span>●</span> Sistema SISIN
        </a>
        <span class="badge-title">Menú Principal</span>
    </nav>
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
</body>
</html>