<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    public const CARGOS = ['COORDINADOR', 'DOCENTE', 'ASISTENTE'];

    protected $table = 'personal_programa_estudio';

    protected $fillable = ['personal_id', 'programa_estudio_id', 'cargo'];

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class);
    }

    public function programaEstudio(): BelongsTo
    {
        return $this->belongsTo(ProgramaEstudio::class);
    }
}
