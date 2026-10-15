<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'username'     => 'ADMIN',
            'password'     => '123456',
            'tipo_usuario' => 'ADMINISTRADOR', 
            'activo'       => true, 
        ]);
    }
}