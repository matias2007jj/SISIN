<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marca extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'marcas';

    protected $fillable = ['programa_estudio_id', 'nombre'];

    public function programaEstudio(): BelongsTo
    {
        return $this->belongsTo(ProgramaEstudio::class);
    }
}
