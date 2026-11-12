<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Scopes\InstitucionScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class Usuario extends Authenticatable
{
    use Auditable, SoftDeletes;

    public const ADMIN_INSTITUCION = 'ADMIN_INSTITUCION';

    /** Texto que se muestra al usuario para cada tipo_usuario. */
    public const ROLES = [
        'ADMINISTRADOR'     => 'Superusuario',
        'ADMIN_INSTITUCION' => 'Administrador de institución',
        'COORDINADOR'       => 'Coordinador',
        'ASISTENTE'         => 'Asistente',
        'CONSULTA'          => 'Consulta',
    ];

    protected $table = 'usuarios';

    protected $fillable = ['username', 'password', 'personal_id', 'institucion_id', 'tipo_usuario', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',   // se hashea sola al asignarla
            'activo'   => 'boolean',
        ];
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class)->withoutGlobalScope(InstitucionScope::class);
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class)->withoutGlobalScope(InstitucionScope::class);
    }

    public function programas(): BelongsToMany
    {
        return $this->belongsToMany(ProgramaEstudio::class, 'usuario_programa_estudio')
            ->withPivot('acceso')
            ->withTimestamps();
    }

    public function tieneAcceso(int $programaId): bool
    {
        return $this->programas()
            ->where('programas_estudio.id', $programaId)
            ->wherePivot('acceso', true)
            ->exists();
    }

    public function esAdministrador(): bool
    {
        return $this->tipo_usuario === 'ADMINISTRADOR';
    }

    /** Superusuario = administrador SIN institución: ve y controla todas. */
    public function esSuperusuario(): bool
    {
        return $this->tipo_usuario === 'ADMINISTRADOR' && $this->institucion_id === null;
    }

    /** Quién puede crear/editar usuarios: el superusuario y el administrador de institución. */
    public function puedeAdministrarUsuarios(): bool
    {
        return $this->esSuperusuario() || $this->tipo_usuario === self::ADMIN_INSTITUCION;
    }

    protected function rolEtiqueta(): Attribute
    {
        return Attribute::get(fn () => self::ROLES[$this->tipo_usuario] ?? $this->tipo_usuario);
    }

    /** Institución con la que está trabajando ahora (null = todas, solo superusuario). */
    public function institucionActiva(): ?Institucion
    {
        if ($this->institucion_id) {
            return $this->institucion;
        }

        if ($this->esSuperusuario() && session('institucion_id')) {
            return Institucion::withoutGlobalScope(InstitucionScope::class)->find(session('institucion_id'));
        }

        return null;
    }

    /** Cargos que tiene en la institución, si el usuario está vinculado a una persona del personal. */
    public function cargos(): Collection
    {
        if (! $this->personal_id) {
            return collect();
        }

        return Asignacion::with('programaEstudio')
            ->where('personal_id', $this->personal_id)
            ->get()
            ->map(fn ($a) => $a->cargo . ' · ' . ($a->programaEstudio?->nombre ?? '—'));
    }

    /** Programa con el que arranca la sesión. */
    public function programaPorDefecto(): ?ProgramaEstudio
    {
        $q = ProgramaEstudio::where('activo', true)->orderBy('nombre');   // ya filtrado por institución

        if ($this->esSuperusuario() || $this->tipo_usuario === self::ADMIN_INSTITUCION) {
            return $q->first();
        }

        return $q->whereHas('usuarios', fn ($u) => $u
                ->where('usuarios.id', $this->id)
                ->where('usuario_programa_estudio.acceso', true))
            ->first();
    }
}
