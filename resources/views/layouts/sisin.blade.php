<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Menú Principal - SISIN')</title>

    <link rel="icon" type="image/png" href="{{ asset('img/SISIN_ICONO.png') }}?v=2" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/SISIN_ICONO.png') }}?v=2" />

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
        document.querySelectorAll('.sidebar-group-toggle, .submenu-toggle').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = btn.parentElement;
                const isOpen = parent.classList.toggle('open');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        const sidebar = document.getElementById('sidebarSisin');
        const backdrop = document.getElementById('sidebarBackdrop');
        const btnOpen = document.getElementById('btnToggleSidebar');
        const btnClose = document.getElementById('btnCloseSidebar');

        function toggleSidebar(open) {
            if (!sidebar) return;
            sidebar.classList.toggle('open', open);
            if (backdrop) backdrop.classList.toggle('show', open);
            if (btnOpen) btnOpen.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        if (btnOpen) btnOpen.addEventListener('click', () => toggleSidebar(true));
        if (btnClose) btnClose.addEventListener('click', () => toggleSidebar(false));
        if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));
    </script>

    @stack('scripts')
</body>

</html>