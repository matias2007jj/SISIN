<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {

        foreach (['NUEVO', 'BUENO', 'REGULAR', 'MALO'] as $nombre) {
            Estado::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
