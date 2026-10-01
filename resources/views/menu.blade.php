<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - SISIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-body: #070d0b;
            --bg-nav: #09120f;
            --bg-card: #0d1714;
            --border-card: #14241f;
            --accent-green: #00d68f;
            --accent-green-hover: #00ffaa;
            --text-light: #e6f1ed;
            --text-muted: #889e96;
            --btn-secondary-bg: #14241f;
            --btn-secondary-border: #1f3b33;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: var(--bg-nav);
            border-bottom: 1px solid var(--border-card);
        }

        .navbar-custom .navbar-brand {
            color: var(--text-light);
            font-size: 1.15rem;
        }

        .navbar-custom .navbar-brand span {
            color: var(--accent-green);
        }

        .navbar-custom .badge-title {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
      
        .menu-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 14px;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            border-color: #1f3b33;
        }

        .menu-card .card-title {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .menu-card .card-text {
            color: var(--text-muted) !important;
            font-size: 0.9rem;
        }

        .btn-green {
            background-color: var(--accent-green);
            color: #032014;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-green:hover {
            background-color: var(--accent-green-hover);
            color: #000;
        }

        .btn-secondary-custom {
            background-color: var(--btn-secondary-bg);
            border: 1px solid var(--btn-secondary-border);
            color: var(--text-light);
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-secondary-custom:hover {
            background-color: #1a2f29;
            border-color: var(--accent-green);
            color: var(--text-light);
        }
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