<?php

namespace Database\Seeders;

use App\Models\Institucion;
use App\Models\ProgramaEstudio;
use Illuminate\Database\Seeder;

class InstitucionSeeder extends Seeder
{
    public function run(): void
    {
        $institucion = Institucion::firstOrCreate(
            ['ruc' => '11111111111'],   // RUC de ejemplo del script original: cámbialo por el real
            [
                'nombre'    => 'Instituto de Educación Superior Tecnológico Público Julio César Tello',
                'direccion' => 'Villa El Salvador',
            ]
        );

        $programas = [
            'Administración de Empresas',
            'Desarrollo de Sistemas de Información',
            'Contabilidad',
            'Electricidad Industrial',
            'Mecanica Automotriz',
            'Mecanica de Producción',
            'Secretariado Ejecutivo',
        ];

        foreach ($programas as $nombre) {
            ProgramaEstudio::firstOrCreate(
                ['institucion_id' => $institucion->id, 'nombre' => mb_strtoupper($nombre)],
                ['activo' => true]
            );
        }
    }
}
