<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <title>Sistema SISIN | Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('img/SISIN_ICONO.png') }}?v=2" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/SISIN_ICONO.png') }}?v=2" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="auth-split-body">
    @php
        $buscarImg = function (string $nombre) {
            foreach (['png', 'jpg', 'jpeg', 'webp', 'svg'] as $ext) {
                $ruta = "img/{$nombre}.{$ext}";
                if (file_exists(public_path($ruta))) {
                    return asset($ruta);
                }
            }
            return null;
        };

        $iconoUrl = $buscarImg('SISIN_ICONO');
        $logoUrl  = $buscarImg('SISIN_LOGO') ?? $iconoUrl;
    @endphp

    <div class="auth-split-wrapper">
        {{-- Panel izquierdo: Marca + Formulario --}}
        <section class="auth-left-panel">
            <header class="auth-brand">
                <span class="brand-logo" aria-hidden="true">
                    @if ($iconoUrl)
                        <img src="{{ $iconoUrl }}" alt="Logo SISIN">
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#10b981">
                            <rect x="10" y="2" width="4.5" height="4.5" rx="1" transform="rotate(45 12.25 4.25)"/>
                            <rect x="16" y="8" width="4.5" height="4.5" rx="1" transform="rotate(45 18.25 10.25)"/>
                            <rect x="4" y="8" width="4.5" height="4.5" rx="1" transform="rotate(45 6.25 10.25)"/>
                            <rect x="10" y="14" width="4.5" height="4.5" rx="1" transform="rotate(45 12.25 16.25)"/>
                        </svg>
                    @endif
                </span>
                <span class="brand-name">Sistema SISIN</span>
            </header>

            <div class="auth-form-center">
                <div class="auth-form-box">
                    <p class="auth-eyebrow">Gestiona tu inventario institucional</p>
                    <h1 class="auth-title">Iniciar sesión en SISIN</h1>

                    <form id="loginForm" onsubmit="return false;" novalidate>
                        <div class="notched-group">
                            <label for="usu" class="notched-label">Usuario o correo</label>
                            <input
                                type="text"
                                class="notched-input"
                                id="usu"
                                placeholder="usuario@institucion.edu.pe"
                                autocomplete="username"
                                autofocus
                            />
                            <span class="input-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                            </span>
                        </div>

                        <div class="notched-group">
                            <label for="pass" class="notched-label">Contraseña</label>
                            <input
                                type="password"
                                class="notched-input"
                                id="pass"
                                placeholder="••••••••"
                                autocomplete="current-password"
                            />
                            <button type="button" class="input-icon btn-toggle-pass" id="togglePass" aria-label="Mostrar u ocultar contraseña">
                                <svg id="iconEyeOpen" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="iconEyeClosed" class="d-none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" y1="2" x2="22" y2="22"/>
                                </svg>
                            </button>
                        </div>

                        <button class="btn btn-sisin-submit" id="btn_acceso" type="button">
                            Ingresar al sistema
                        </button>
                    </form>
                </div>
            </div>

            <footer class="auth-footer">
                <span>Sistema de Inventario Institucional · <strong>SISIN</strong></span>
            </footer>
        </section>

        {{-- Panel derecho: Ondas esmeralda + Logo SISIN --}}
        <aside class="auth-right-panel d-none d-lg-flex" aria-hidden="true">
            <svg class="fluid-waves" viewBox="0 0 900 1100" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#0b1114"/>
                        <stop offset="45%" stop-color="#0d2620"/>
                        <stop offset="100%" stop-color="#081613"/>
                    </linearGradient>
                    <linearGradient id="waveEmerald" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#042f2e"/>
                        <stop offset="50%" stop-color="#059669"/>
                        <stop offset="100%" stop-color="#34d399"/>
                    </linearGradient>
                    <linearGradient id="waveMint" x1="100%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#6ee7b7" stop-opacity="0.72"/>
                        <stop offset="55%" stop-color="#10b981" stop-opacity="0.42"/>
                        <stop offset="100%" stop-color="#0b1114" stop-opacity="0.92"/>
                    </linearGradient>
                    <linearGradient id="waveSlate" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#1e293b" stop-opacity="0.85"/>
                        <stop offset="60%" stop-color="#0f766e" stop-opacity="0.55"/>
                        <stop offset="100%" stop-color="#064e3b" stop-opacity="0.9"/>
                    </linearGradient>
                    <radialGradient id="orbGlow" cx="52%" cy="46%" r="48%">
                        <stop offset="0%" stop-color="#10b981" stop-opacity="0.42"/>
                        <stop offset="60%" stop-color="#059669" stop-opacity="0.12"/>
                        <stop offset="100%" stop-color="#0b1114" stop-opacity="0"/>
                    </radialGradient>
                </defs>

                <rect width="900" height="1100" fill="url(#bgGrad)"/>
                <rect width="900" height="1100" fill="url(#orbGlow)"/>

                <path d="M-80,180 C160,60 320,380 560,240 C760,120 840,-40 980,80 L980,520 C760,420 620,660 380,540 C160,430 40,620 -80,510 Z" fill="url(#waveSlate)"/>
                <path d="M-60,490 C180,340 360,620 610,450 C800,320 860,190 980,290 L980,790 C780,690 590,890 340,760 C120,650 20,830 -60,740 Z" fill="url(#waveEmerald)" opacity="0.82"/>
                <path d="M-50,780 C190,630 410,870 670,710 C820,615 890,520 980,610 L980,1150 L-50,1150 Z" fill="url(#waveMint)"/>
                <path d="M120,-40 C320,160 220,420 470,560 C680,675 790,530 960,680 L960,360 C760,250 660,410 470,290 C310,190 390,20 260,-40 Z" fill="url(#waveMint)" opacity="0.52"/>

                <circle cx="660" cy="240" r="6" fill="#a7f3d0" fill-opacity="0.5"/>
                <circle cx="240" cy="310" r="4.5" fill="#a7f3d0" fill-opacity="0.45"/>
                <circle cx="720" cy="540" r="5" fill="#6ee7b7" fill-opacity="0.4"/>
                <circle cx="210" cy="690" r="4" fill="#a7f3d0" fill-opacity="0.4"/>
            </svg>

            {{-- Imagen SISIN_LOGO centrada --}}
            @if ($logoUrl)
                <div class="laptop-showcase">
                    <img src="{{ $logoUrl }}" alt="Logo Sistema SISIN" class="sisin-logo-img">
                </div>
            @endif

            {{-- Indicadores inferiores derechos --}}
            <div class="art-dots" aria-hidden="true">
                <span class="active"></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/mensaje.js') }}"></script>
    <script src="{{ asset('js/acceso.js') }}"></script>

</body>

</html>