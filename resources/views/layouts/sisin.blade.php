<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Menú Principal - SISIN')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>

    @include('partials.navegacion')

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.submenu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggle.parentElement.classList.toggle('open');
            });
        });

        document.querySelectorAll('.navbar-custom .dropdown').forEach(function(dd) {
            dd.addEventListener('hidden.bs.dropdown', function() {
                dd.querySelectorAll('.dropdown-submenu.open').forEach(function(s) {
                    s.classList.remove('open');
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>