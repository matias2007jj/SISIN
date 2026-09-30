<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * Rellena created_by / updated_by con el usuario autenticado.
 * Reemplaza idUsuarioCrea / idUsuarioModifica del script original.
 */
trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::creating(function ($model) {
            $model->created_by ??= Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
