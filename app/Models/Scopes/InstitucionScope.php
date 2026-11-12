<?php

namespace App\Models\Scopes;

use App\Models\Institucion;
use App\Models\ProgramaEstudio;
use App\Support\ContextoInstitucion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/** Filtra automáticamente cualquier consulta a la institución activa de la sesión. */
class InstitucionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $id = ContextoInstitucion::id();

        if ($id === null) {
            return;                                    // superusuario viendo todo
        }

        if ($model instanceof Institucion) {
            $builder->where($model->getTable() . '.id', $id);
            return;
        }

        if ($model instanceof ProgramaEstudio) {
            $builder->where($model->getTable() . '.institucion_id', $id);
            return;
        }

        // Resto de modelos: llegan a la institución por la cadena de relaciones
        $builder->whereHas(
            $model->rutaInstitucion(),
            fn ($q) => $q->where('programas_estudio.institucion_id', $id)
        );
    }
}
