<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventarioDetalle extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'inventario_detalles';

    protected $fillable = [
        'inventario_id', 'bien_id',
        'grupo_id', 'nombre_grupo', 'nro_drelm', 'cbi',
        'tipo_id', 'nombre_tipo', 'descripcion',
        'marca_id', 'nombre_marca', 'modelo_id', 'nombre_modelo',
        'serie_medida', 'estado_id', 'nombre_estado', 'otras_especificaciones',
    ];

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    /** Copia el estado actual del bien (la "foto" del inventario). */
    public static function datosDesdeBien(Bien $bien, ?string $nroDrelm = null): array
    {
        $bien->loadMissing(['grupo', 'tipo', 'marca', 'modelo', 'estado']);

        return [
            'bien_id'                => $bien->id,
            'grupo_id'               => $bien->grupo_id,
            'nombre_grupo'           => $bien->grupo?->nombre,
            'nro_drelm'              => $nroDrelm,
            'cbi'                    => $bien->cbi,
            'tipo_id'                => $bien->tipo_id,
            'nombre_tipo'            => $bien->tipo?->nombre,
            'descripcion'            => $bien->descripcion,
            'marca_id'               => $bien->marca_id,
            'nombre_marca'           => $bien->marca?->nombre,   // null-safe: bienes sin marca no se pierden
            'modelo_id'              => $bien->modelo_id,
            'nombre_modelo'          => $bien->modelo?->nombre,
            'serie_medida'           => $bien->serie_medida,
            'estado_id'              => $bien->estado_id,
            'nombre_estado'          => $bien->estado?->nombre,
            'otras_especificaciones' => $bien->otras_especificaciones,
        ];
    }
}
