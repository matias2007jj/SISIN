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
            'username'     => 'ADMIN', // Coincide con la vista y el auth controller
            'password'     => '123456', // Se hasheará automáticamente gracias al cast del modelo
            'tipo_usuario' => 'ADMINISTRADOR', // Coincide con tu método esAdministrador()
            'activo'       => true, // Requerido por tu $credentials en AuthController
        ]);
    }
}