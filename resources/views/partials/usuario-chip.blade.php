@php
    $u    = auth()->user();
    $inst = $u->institucionActiva();
    $sub  = $u->esSuperusuario()
        ? ($inst ? 'Viendo: ' . $inst->nombre : 'Todas las instituciones')
        : ($inst->nombre ?? 'Sin institución');
@endphp
<span class="avatar">{{ mb_strtoupper(mb_substr($u->username ?? 'U', 0, 1)) }}</span>
<span class="user-info">
    <span class="user-name">{{ $u->username }}</span>
    <small class="user-sub" title="{{ $sub }} · {{ $u->rol_etiqueta }}">{{ $sub }} · {{ $u->rol_etiqueta }}</small>
</span>
