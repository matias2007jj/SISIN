<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\DeInstitucion;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use Auditable, DeInstitucion, SoftDeletes;

    protected $table = 'personal';

    protected $fillable = ['apellidos', 'nombres', 'dni', 'celular', 'email', 'direccion'];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    /** "APELLIDOS NOMBRES", igual que el CONCAT del procedimiento original. */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->apellidos} {$this->nombres}"));
    }

    public function rutaInstitucion(): string
    {
        return 'asignaciones.programaEstudio';
    }
}
