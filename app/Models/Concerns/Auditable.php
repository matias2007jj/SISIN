<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;


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
