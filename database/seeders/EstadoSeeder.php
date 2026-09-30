<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        // Estados de conservación. "SIN MARCA (S/M)" y "SIN NUMERO (S/N)" del script original
        // se dejaron fuera: eso se representa con marca_id / serie_medida nulos.
        foreach (['NUEVO', 'BUENO', 'REGULAR', 'MALO'] as $nombre) {
            Estado::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
