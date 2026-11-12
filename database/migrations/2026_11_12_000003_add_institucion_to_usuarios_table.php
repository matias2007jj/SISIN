<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nuevo rol: el usuario que se crea junto con cada institución.
        DB::statement("ALTER TABLE usuarios MODIFY tipo_usuario
            ENUM('ADMINISTRADOR','ADMIN_INSTITUCION','COORDINADOR','ASISTENTE','CONSULTA')
            NOT NULL DEFAULT 'CONSULTA'");

        Schema::table('usuarios', function (Blueprint $table) {
            // NULL = usuario global (superusuario). Con valor = pertenece a esa institución.
            $table->foreignId('institucion_id')->nullable()->after('personal_id')
                  ->constrained('instituciones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institucion_id');
        });

        DB::table('usuarios')->where('tipo_usuario', 'ADMIN_INSTITUCION')->update(['tipo_usuario' => 'CONSULTA']);
        DB::statement("ALTER TABLE usuarios MODIFY tipo_usuario
            ENUM('ADMINISTRADOR','COORDINADOR','ASISTENTE','CONSULTA') NOT NULL DEFAULT 'CONSULTA'");
    }
};
