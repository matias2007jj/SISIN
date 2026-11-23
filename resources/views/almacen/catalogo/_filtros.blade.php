@php
    $user = auth()->user();
    $esAdminInstitucion = $user->tipo_usuario === 'ADMIN_INSTITUCION';

    $mostrarComboInstitucion = $esSuper && isset($instituciones) && count($instituciones) > 0;
    $mostrarComboPrograma    = ($esSuper || $esAdminInstitucion) && isset($programasEstudio) && count($programasEstudio) > 0;
@endphp

@if ($mostrarComboInstitucion || $mostrarComboPrograma)
<div class="row g-3 align-items-end mb-4">
    @if ($mostrarComboInstitucion)
    <div class="col-12 col-md-4 col-lg-3">
        <label for="filtroInstitucion" class="form-label text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; color: #94a3b8; letter-spacing: 0.5px;">
            Institución:
        </label>
        <select id="filtroInstitucion" class="form-select form-select-sm bg-dark text-white border-secondary text-uppercase" style="height: 38px;">
            <option value="">TODAS LAS INSTITUCIONES</option>
            @foreach ($instituciones as $inst)
                <option value="{{ $inst->id }}">{{ $inst->nombre }}</option>
            @endforeach
        </select>
    </div>
    @endif

    @if ($mostrarComboPrograma)
    <div class="col-12 col-md-4 col-lg-4">
        <label for="filtroPrograma" class="form-label text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; color: #94a3b8; letter-spacing: 0.5px;">
            Programa de Estudio:
        </label>
        <select id="filtroPrograma" class="form-select form-select-sm bg-dark text-white border-secondary text-uppercase" style="height: 38px;">
            <option value="">TODOS LOS PROGRAMAS</option>
            @foreach ($programasEstudio as $prog)
                <option value="{{ $prog->id }}" data-institucion-id="{{ $prog->institucion_id ?? '' }}">
                    {{ $prog->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="col-12 col-md-auto">
        <button type="button" id="btnLimpiarFiltros" class="btn btn-outline-secondary btn-sm px-3 d-none" style="height: 38px;">
            Limpiar filtros
        </button>
    </div>
</div>
@endif