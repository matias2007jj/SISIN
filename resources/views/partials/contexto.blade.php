@php
    $u      = auth()->user();
    $inst   = $u->institucionActiva();
    $cargos = $u->cargos();
@endphp
<li><h6 class="dropdown-header">{{ $u->rol_etiqueta }}</h6></li>
<li><span class="dropdown-item-text small">{{ $inst->nombre ?? 'Todas las instituciones' }}</span></li>
@foreach ($cargos as $cargo)
    <li><span class="dropdown-item-text small text-muted">{{ $cargo }}</span></li>
@endforeach

@if ($u->esSuperusuario())
    <li>
        <form method="POST" action="{{ route('contexto.institucion') }}" class="px-3 py-2" data-bs-theme="dark">
            @csrf
            <label class="small text-muted mb-1" for="selInstitucion">Institución activa</label>
            <select id="selInstitucion" name="institucion_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todas las instituciones</option>
                @foreach (\App\Models\Institucion::withoutGlobalScope(\App\Models\Scopes\InstitucionScope::class)->orderBy('nombre')->get(['id', 'nombre']) as $i)
                    <option value="{{ $i->id }}" @selected((int) session('institucion_id') === $i->id)>{{ $i->nombre }}</option>
                @endforeach
            </select>
        </form>
    </li>
@endif

{{-- Programa de estudio activo: define con qué bienes, salas, grupos, etc. se trabaja --}}
@php
    $mostrarPrograma = ! $u->esSuperusuario() || $inst;      // el superusuario primero elige institución
    $programasMenu   = $mostrarPrograma
        ? \App\Models\ProgramaEstudio::whereIn('id', $u->idsProgramasPermitidos())->orderBy('nombre')->get(['id', 'nombre'])
        : collect();
    $programaActivoId = $programasMenu->isNotEmpty() ? \App\Support\ContextoInstitucion::programaId() : null;
@endphp
@if ($programasMenu->count() > 1)
    <li>
        <form method="POST" action="{{ route('contexto.programa') }}" class="px-3 py-2" data-bs-theme="dark">
            @csrf
            <label class="small text-muted mb-1" for="selPrograma">Programa activo</label>
            <select id="selPrograma" name="programa_estudio_id" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach ($programasMenu as $p)
                    <option value="{{ $p->id }}" @selected($programaActivoId === $p->id)>{{ $p->nombre }}</option>
                @endforeach
            </select>
        </form>
    </li>
@elseif ($programasMenu->count() === 1)
    <li><span class="dropdown-item-text small">Programa: {{ $programasMenu->first()->nombre }}</span></li>
@endif
<li><hr class="dropdown-divider"></li>
