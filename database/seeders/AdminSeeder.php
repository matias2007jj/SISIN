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
                'password'     => env('SEED_ADMIN_PASSWORD', 'cambiar-esto-ya'),
                'tipo_usuario' => 'ADMINISTRADOR',
                'activo'       => true,
            ]
        );

        $accesos = ProgramaEstudio::pluck('id')
            ->mapWithKeys(fn ($id) => [$id => ['acceso' => true]])
            ->all();

        $admin->programas()->syncWithoutDetaching($accesos);
    }
}
