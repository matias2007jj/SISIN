<?php

namespace App\Models\Concerns;

use App\Models\Scopes\InstitucionScope;

/**
 * Marca un modelo como "propiedad" de una institución.
 * Sobrescribe rutaInstitucion() si el camino hasta programaEstudio es más largo.
 */
trait DeInstitucion
{
    protected static function bootDeInstitucion(): void
    {
        static::addGlobalScope(new InstitucionScope());
    }

    /** Relación (o cadena de relaciones) que termina en ProgramaEstudio. */
    public function rutaInstitucion(): string
    {
        return 'programaEstudio';
    }
}
