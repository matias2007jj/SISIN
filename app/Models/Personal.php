<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'personal';

    protected $fillable = ['apellidos', 'nombres', 'dni', 'celular', 'email', 'direccion'];

    /** "APELLIDOS NOMBRES", igual que el CONCAT del procedimiento original. */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->apellidos} {$this->nombres}"));
    }
}
