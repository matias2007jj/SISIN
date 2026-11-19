{{-- Barra de navegación única (la usan layouts.sisin y plantilla.plantilla). Cada rol ve solo lo que puede usar. --}}
@php
    $u = auth()->user();
    $verAlmacen = $u->puede('bienes.ver') || $u->puede('inventarios.ver') || $u->puede('catalogos.ver');
    $catalogos  = ['grupo' => 'Grupo', 'sala' => 'Sala', 'tipo' => 'Tipo', 'marca' => 'Marca', 'modelo' => 'Modelo', 'estado' => 'Estado'];
    $catalogos  = array_filter($catalogos, fn ($nombre, $slug) => $u->puede("catalogo.$slug.ver"), ARRAY_FILTER_USE_BOTH);
@endphp
<nav class="navbar navbar-expand-xl navbar-custom mb-4">
    <div class="container">
        <div class="nav-pill d-flex flex-wrap align-items-center justify-content-between">
            <a class="navbar-brand fw-bold m-0" href="{{ url('/menu') }}">
                Sistema SISIN
            </a>

            <button class="btn btn-toggle-menu d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#topMenu" aria-controls="topMenu" aria-expanded="false" aria-label="Abrir menú">
                Menú
            </button>

            <div class="collapse navbar-collapse" id="topMenu">
                <ul class="navbar-nav top-nav me-auto ms-xl-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('menu') ? 'active' : '' }}" href="{{ url('/menu') }}" @if(request()->is('menu')) aria-current="page" @endif>
                            Home
                        </a>
                    </li>

                    @if ($verAlmacen)
                        <li class="nav-divider d-none d-xl-block" aria-hidden="true"></li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('catalogo.*', 'bienes.*', 'inventarios.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Almacén
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                @if (count($catalogos))
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item submenu-toggle" href="#">
                                            Tablas Generales
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-custom submenu">
                                            @foreach ($catalogos as $slug => $nombre)
                                                <li>
                                                    <a class="dropdown-item {{ request()->route('catalogo') === $slug ? 'active' : '' }}" href="{{ route('catalogo.index', $slug) }}">{{ $nombre }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                @if ($u->puede('bienes.ver'))
                                    <li><a class="dropdown-item {{ request()->routeIs('bienes.*') ? 'active' : '' }}" href="{{ route('bienes.index') }}">Bienes</a></li>
                                @endif

                                @if ($u->puede('inventarios.ver'))
                                    <li><a class="dropdown-item {{ request()->routeIs('inventarios.*') ? 'active' : '' }}" href="{{ route('inventarios.index') }}">Toma de Inventario</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Superusuario: lista de instituciones. Director: su institución. Coordinador: su(s) programa(s). --}}
                    @if ($u->esSuperusuario())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('instituciones.*') ? 'active' : '' }}" href="{{ route('instituciones.index') }}">Instituciones</a>
                        </li>
                    @elseif ($u->institucion_id && $u->puede('institucion.ver'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('instituciones.*') ? 'active' : '' }}" href="{{ route('instituciones.show', $u->institucion_id) }}">
                                {{ $u->puede('institucion.gestionar') ? 'Mi institución' : 'Mi programa' }}
                            </a>
                        </li>
                    @endif

                    @if ($u->puedeAdministrarUsuarios())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">Usuarios</a>
                        </li>
                    @endif
                </ul>

                <div class="dropdown user-menu mt-3 mt-xl-0">
                    <a class="user-chip d-flex align-items-center gap-2 text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @include('partials.usuario-chip')
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                        @include('partials.contexto')
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('cuenta.*') ? 'active' : '' }}" href="{{ route('cuenta.edit') }}">Mi cuenta</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-logout">
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
