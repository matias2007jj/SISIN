<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Scopes\InstitucionScope;
use App\Support\Permisos;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class Usuario extends Authenticatable
{
    use Auditable, SoftDeletes;

    public const ADMIN_INSTITUCION = 'ADMIN_INSTITUCION';
    public const COORDINADOR       = 'COORDINADOR';
    public const ASISTENTE         = 'ASISTENTE';
    public const DOCENTE           = 'DOCENTE';

    /** Texto que se muestra al usuario para cada tipo_usuario. */
    public const ROLES = [
        'ADMINISTRADOR'     => 'Superusuario',
        'ADMIN_INSTITUCION' => 'Administrador de institución',
        'COORDINADOR'       => 'Coordinador',
        'ASISTENTE'         => 'Asistente',
        'DOCENTE'           => 'Docente',
    ];

    /** Memoria por petición de los programas permitidos (se calcula una sola vez). */
    private ?array $idsProgramas = null;

    protected $table = 'usuarios';

    /**
     * Reglas del nombre de usuario: puede ser un texto simple (ADMIN, un DNI)
     * o con forma de correo (institucion@gmail.edu.pe). Único, aunque esté eliminado.
     */
    public static function reglasUsername(?int $ignorarId = null): array
    {
        return [
            'string',
            'max:100',
            'regex:/^[A-Za-z0-9._@+-]+$/',
            Rule::unique('usuarios', 'username')->ignore($ignorarId),
        ];
    }

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
        return $this->puede('usuarios.gestionar');
    }

    /** Un ADMINISTRADOR atado a una institución se trata como director de esa institución. */
    public function rolEfectivo(): string
    {
        return $this->tipo_usuario === 'ADMINISTRADOR' ? self::ADMIN_INSTITUCION : (string) $this->tipo_usuario;
    }

    /** ¿Tiene este permiso? (ver App\Support\Permisos). El superusuario siempre puede. */
    public function puede(string $permiso): bool
    {
        if ($this->esSuperusuario()) {
            return true;
        }

        return in_array($permiso, Permisos::deRol($this->rolEfectivo()), true);
    }

    /** Superusuario y director ven todos los programas de la institución; el resto, solo los suyos. */
    public function veTodaLaInstitucion(): bool
    {
        return $this->esSuperusuario() || $this->rolEfectivo() === self::ADMIN_INSTITUCION;
    }

    /** IDs de los programas de estudio activos con los que puede trabajar, ordenados por nombre. */
    public function idsProgramasPermitidos(): array
    {
        if ($this->idsProgramas !== null) {
            return $this->idsProgramas;
        }

        // ProgramaEstudio ya trae el filtro de institución (InstitucionScope)
        $q = ProgramaEstudio::query()
            ->where('programas_estudio.activo', true)
            ->orderBy('programas_estudio.nombre');

        if (! $this->veTodaLaInstitucion()) {
            $q->whereIn('programas_estudio.id', function ($sub) {
                $sub->select('programa_estudio_id')
                    ->from('usuario_programa_estudio')
                    ->where('usuario_id', $this->id)
                    ->where('acceso', true);
            });
        }

        return $this->idsProgramas = $q->pluck('programas_estudio.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function puedeUsarPrograma(int $programaId): bool
    {
        return in_array($programaId, $this->idsProgramasPermitidos(), true);
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
        $id = $this->idsProgramasPermitidos()[0] ?? null;

        return $id ? ProgramaEstudio::find($id) : null;
    }
}
