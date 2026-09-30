<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'inventarios';

    protected $fillable = [
        'programa_estudio_id', 'serie', 'numero',
        'sala_id', 'nombre_sala',
        'coordinador_id', 'nombre_coordinador',
        'asistente_t1_id', 'nombre_asistente_t1',
        'asistente_t2_id', 'nombre_asistente_t2',
        'fecha_entrega', 'observacion', 'estado_inventario',
    ];

    protected $casts = ['fecha_entrega' => 'date'];

    public function programaEstudio(): BelongsTo
    {
        return $this->belongsTo(ProgramaEstudio::class);
    }

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(InventarioDetalle::class);
    }

    public function estaAbierto(): bool
    {
        return $this->estado_inventario === 'ABIERTO';
    }
}
