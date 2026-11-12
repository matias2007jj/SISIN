<?php

namespace Database\Seeders;

use App\Models\Institucion;
use App\Models\Scopes\InstitucionScope;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Crea el usuario de acceso de las instituciones que ya existían y no tienen uno.
 * Usuario = RUC; la contraseña se genera y se muestra una sola vez en consola.
 *   php artisan db:seed --class=UsuariosInstitucionSeeder
 */
class UsuariosInstitucionSeeder extends Seeder
{
    public function run(): void
    {
        Institucion::withoutGlobalScope(InstitucionScope::class)
            ->whereDoesntHave('usuarios')
            ->each(function (Institucion $institucion) {
                $clave = Str::password(10, symbols: false);

                Usuario::firstOrCreate(
                    ['username' => $institucion->ruc],
                    [
                        'password'       => $clave,
                        'institucion_id' => $institucion->id,
                        'tipo_usuario'   => Usuario::ADMIN_INSTITUCION,
                        'activo'         => true,
                    ]
                );

                $this->command?->info("{$institucion->nombre} → usuario: {$institucion->ruc} | clave: {$clave}");
            });
    }
}
