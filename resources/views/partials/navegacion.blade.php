{{-- Barra de navegación lateral única (la usan layouts.sisin y plantilla.plantilla). --}}
@php
    $u = auth()->user();
    $verAlmacen = in_array(true, [
        $u->puede('bienes.ver'),
        $u->puede('inventarios.ver'),
        $u->puede('catalogos.ver'),
    ], true);
    $catalogos = ['grupo' => 'Grupo', 'sala' => 'Sala', 'tipo' => 'Tipo', 'marca' => 'Marca', 'modelo' => 'Modelo', 'estado' => 'Estado'];
    $catalogos = array_filter($catalogos, fn ($nombre, $slug) => $u->puede("catalogo.$slug.ver"), ARRAY_FILTER_USE_BOTH);

    $enAlmacen = request()->routeIs('catalogo.*', 'bienes.*', 'inventarios.*');
    $enCatalogos = request()->routeIs('catalogo.*');
@endphp

{{-- Barra superior compacta solo visible en móvil/tablet (< 992px) --}}
<header class="mobile-topbar d-lg-none">
    <div class="mobile-topbar-bar">
        <a class="navbar-brand fw-bold m-0" href="{{ url('/menu') }}">
            <span class="brand-logo" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#10b981">
                    <rect x="10" y="2" width="4.5" height="4.5" rx="1" transform="rotate(45 12.25 4.25)" />
                    <rect x="16" y="8" width="4.5" height="4.5" rx="1" transform="rotate(45 18.25 10.25)" />
                    <rect x="4" y="8" width="4.5" height="4.5" rx="1" transform="rotate(45 6.25 10.25)" />
                    <rect x="10" y="14" width="4.5" height="4.5" rx="1" transform="rotate(45 12.25 16.25)" />
                </svg>
            </span>
            <span>Sistema SISIN</span>
        </a>
        <button class="btn btn-toggle-menu" type="button" id="btnToggleSidebar" aria-controls="sidebarSisin" aria-expanded="false" aria-label="Abrir menú">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
            <span>Menú</span>
        </button>
    </div>
</header>

{{-- Fondo oscuro al abrir el menú en móvil --}}
<div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop"></div>

{{-- Barra lateral estilo rail minimalista --}}
<aside class="sidebar-sisin" id="sidebarSisin" aria-label="Navegación principal">
    <div class="sidebar-header">
        <a class="navbar-brand m-0" href="{{ url('/menu') }}">
            <span class="brand-logo" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#10b981">
                    <rect x="10" y="2" width="4.5" height="4.5" rx="1" transform="rotate(45 12.25 4.25)" />
                    <rect x="16" y="8" width="4.5" height="4.5" rx="1" transform="rotate(45 18.25 10.25)" />
                    <rect x="4" y="8" width="4.5" height="4.5" rx="1" transform="rotate(45 6.25 10.25)" />
                    <rect x="10" y="14" width="4.5" height="4.5" rx="1" transform="rotate(45 12.25 16.25)" />
                </svg>
            </span>
            <span class="brand-text">Sistema SISIN</span>
        </a>
        <button type="button" class="btn-close-sidebar d-lg-none" id="btnCloseSidebar" aria-label="Cerrar menú">
            &times;
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column sidebar-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('menu') ? 'active' : '' }}" href="{{ url('/menu') }}" @if(request()->is('menu')) aria-current="page" @endif>
                    <span>Home</span>
                </a>
            </li>

            @if ($verAlmacen)
                <li class="nav-item sidebar-group {{ $enAlmacen ? 'open' : '' }}">
                    <button type="button" class="nav-link sidebar-group-toggle {{ $enAlmacen ? 'active' : '' }}" aria-expanded="{{ $enAlmacen ? 'true' : 'false' }}">
                        <span>Almacén</span>
                        <svg class="chevron-icon ms-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </button>

                    <ul class="sidebar-submenu list-unstyled">
                        @if (count($catalogos))
                            <li class="sidebar-subgroup {{ $enCatalogos ? 'open' : '' }}">
                                <button type="button" class="submenu-link submenu-toggle {{ $enCatalogos ? 'active' : '' }}" aria-expanded="{{ $enCatalogos ? 'true' : 'false' }}">
                                    <span>Tablas Generales</span>
                                </button>
                                <ul class="sidebar-subsubmenu list-unstyled">
                                    @foreach ($catalogos as $slug => $nombre)
                                        <li>
                                            <a class="submenu-link {{ request()->route('catalogo') === $slug ? 'active' : '' }}" href="{{ route('catalogo.index', $slug) }}">
                                                {{ $nombre }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        @if ($u->puede('bienes.ver'))
                            <li>
                                <a class="submenu-link {{ request()->routeIs('bienes.*') ? 'active' : '' }}" href="{{ route('bienes.index') }}">
                                    Bienes
                                </a>
                            </li>
                        @endif

                        @if ($u->puede('inventarios.ver'))
                            <li>
                                <a class="submenu-link {{ request()->routeIs('inventarios.*') ? 'active' : '' }}" href="{{ route('inventarios.index') }}">
                                    Toma de Inventario
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ($u->esSuperusuario())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('instituciones.*') ? 'active' : '' }}" href="{{ route('instituciones.index') }}">
                        <span>Instituciones</span>
                    </a>
                </li>
            @elseif ($u->institucion_id && $u->puede('institucion.ver'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('instituciones.*') ? 'active' : '' }}" href="{{ route('instituciones.show', $u->institucion_id) }}">
                        <span>{{ $u->puede('institucion.gestionar') ? 'Mi institución' : 'Mi programa' }}</span>
                    </a>
                </li>
            @endif

            @if ($u->puedeAdministrarUsuarios())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                        <span>Usuarios</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <div class="sidebar-footer">
        {{-- Selector de contexto (Institución / Programa) y Mi cuenta --}}
        <div class="dropup user-menu w-100 mb-2">
            <a class="user-chip d-flex align-items-center gap-2 text-decoration-none w-100" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                @include('partials.usuario-chip')
            </a>
            <ul class="dropdown-menu dropdown-menu-custom w-100">
                @include('partials.contexto')
                @if ($u->esSuperusuario())
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('cuenta.*') ? 'active' : '' }}" href="{{ route('cuenta.edit') }}">Mi cuenta</a>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Botón inferior de Cerrar sesión --}}
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn-sidebar-logout">
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>