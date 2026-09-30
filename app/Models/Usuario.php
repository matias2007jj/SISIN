<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use Auditable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = ['username', 'password', 'personal_id', 'tipo_usuario', 'activo'];

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
        return $this->belongsTo(Personal::class);
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
}
