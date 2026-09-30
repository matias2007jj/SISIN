<?php

namespace Database\Seeders;

use App\Models\ProgramaEstudio;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::firstOrCreate(
            ['username' => 'admin'],
            [
                // Define SEED_ADMIN_PASSWORD en tu .env; el cast "hashed" la guarda cifrada.
                'password'     => env('SEED_ADMIN_PASSWORD', 'cambiar-esto-ya'),
                'tipo_usuario' => 'ADMINISTRADOR',
                'activo'       => true,
            ]
        );

        // Acceso a todos los programas (lo que hacían el trigger y el UPDATE del script original).
        $accesos = ProgramaEstudio::pluck('id')
            ->mapWithKeys(fn ($id) => [$id => ['acceso' => true]])
            ->all();

        $admin->programas()->syncWithoutDetaching($accesos);
    }
}
