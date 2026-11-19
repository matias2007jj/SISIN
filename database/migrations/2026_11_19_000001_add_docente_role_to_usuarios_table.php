<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Se agrega DOCENTE junto a CONSULTA (todavía existente) para poder migrar los datos
        DB::statement("ALTER TABLE usuarios MODIFY tipo_usuario
            ENUM('ADMINISTRADOR','ADMIN_INSTITUCION','COORDINADOR','ASISTENTE','DOCENTE','CONSULTA')
            NOT NULL DEFAULT 'DOCENTE'");

        // 2) Los usuarios "Consulta" pasan a ser "Docente" (mismo permiso: solo ver)
        DB::table('usuarios')->where('tipo_usuario', 'CONSULTA')->update(['tipo_usuario' => 'DOCENTE']);

        // 3) Se retira CONSULTA
        DB::statement("ALTER TABLE usuarios MODIFY tipo_usuario
            ENUM('ADMINISTRADOR','ADMIN_INSTITUCION','COORDINADOR','ASISTENTE','DOCENTE')
            NOT NULL DEFAULT 'DOCENTE'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE usuarios MODIFY tipo_usuario
            ENUM('ADMINISTRADOR','ADMIN_INSTITUCION','COORDINADOR','ASISTENTE','DOCENTE','CONSULTA')
            NOT NULL DEFAULT 'CONSULTA'");

        DB::table('usuarios')->where('tipo_usuario', 'DOCENTE')->update(['tipo_usuario' => 'CONSULTA']);

        DB::statement("ALTER TABLE usuarios MODIFY tipo_usuario
            ENUM('ADMINISTRADOR','ADMIN_INSTITUCION','COORDINADOR','ASISTENTE','CONSULTA')
            NOT NULL DEFAULT 'CONSULTA'");
    }
};
