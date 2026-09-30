<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramaEstudio extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'programas_estudio';

    protected $fillable = [
        'institucion_id', 'nombre', 'abreviatura',
        'coordinador_id', 'asistente_t1_id', 'asistente_t2_id',
        'observacion', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function coordinador(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'coordinador_id');
    }

    public function asistenteT1(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'asistente_t1_id');
    }

    public function asistenteT2(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'asistente_t2_id');
    }

    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_programa_estudio')
            ->withPivot('acceso')
            ->withTimestamps();
    }
}
