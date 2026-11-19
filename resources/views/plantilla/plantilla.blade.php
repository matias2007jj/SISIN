@if(auth()->check() || (session('usuario') && session('id_perfil')))
@php
    $nombreUsuario = auth()->user()->name ?? session('usuario') ?? 'Usuario';
    $nombreUsuario = is_scalar($nombreUsuario) ? (string) $nombreUsuario : 'Usuario';
@endphp
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
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
<body style="background-color: #0b1114; color: #ffffff;">

    @include('partials.navegacion')

    @hasSection('titulo')
        <main class="container plantilla-page pb-5">
            <div class="plantilla-card">
                <header class="plantilla-header">
                    <h2>@yield('titulo')</h2>

                    @if ($__env->hasSection('boton') && ($puedeCrear ?? true))
                        <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#@yield('modal_id', 'modalAgregar')">
                            @yield('boton')
                        </button>
                    @endif
                </header>

                <div class="plantilla-body">
                    @if (session('aviso_error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 mb-4 text-white" style="background-color: rgba(239, 68, 68, 0.2); border-left: 4px solid #ef4444 !important;" role="alert">
                            {{ session('aviso_error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

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