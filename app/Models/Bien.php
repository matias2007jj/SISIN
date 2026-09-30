<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'bienes';   // Eloquent pluralizaría "biens"

    protected $fillable = [
        'sala_id', 'grupo_id', 'cbi', 'tipo_id', 'descripcion',
        'marca_id', 'modelo_id', 'serie_medida', 'estado_id', 'otras_especificaciones',
    ];

    public function sala(): BelongsTo   { return $this->belongsTo(Sala::class); }
    public function grupo(): BelongsTo  { return $this->belongsTo(Grupo::class); }
    public function tipo(): BelongsTo   { return $this->belongsTo(Tipo::class); }
    public function marca(): BelongsTo  { return $this->belongsTo(Marca::class); }
    public function modelo(): BelongsTo { return $this->belongsTo(Modelo::class); }
    public function estado(): BelongsTo { return $this->belongsTo(Estado::class); }
}
