<?php

namespace App\Models\Concerns;

use App\Models\Scopes\InstitucionScope;


trait DeInstitucion
{
    protected static function bootDeInstitucion(): void
    {
        static::addGlobalScope(new InstitucionScope());
    }

    public function rutaInstitucion(): string
    {
        return 'programaEstudio';
    }
}
