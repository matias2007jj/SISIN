<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\DeInstitucion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institucion extends Model
{
    use Auditable, DeInstitucion, SoftDeletes;

    protected $table = 'instituciones';

    public const TIPOS = ['COLEGIO', 'INSTITUTO', 'UNIVERSIDAD'];

    protected $fillable = ['ruc', 'nombre', 'tipo', 'direccion'];

    public function programas(): HasMany
    {
        return $this->hasMany(ProgramaEstudio::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }
}
