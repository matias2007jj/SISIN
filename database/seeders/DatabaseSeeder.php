<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EstadoSeeder::class,
            InstitucionSeeder::class,   // crea la institución y los programas
            AdminSeeder::class,         // después, para darle acceso a todos los programas
        ]);
    }
}
