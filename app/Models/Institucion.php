<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institucion extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'instituciones';

    protected $fillable = ['ruc', 'nombre', 'direccion'];

    public function programas(): HasMany
    {
        return $this->hasMany(ProgramaEstudio::class);
    }
}
