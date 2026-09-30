<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla pivote: a qué programas tiene acceso cada usuario.
        // Se crea solo la fila cuando se otorga acceso; ya no hace falta el trigger
        // que insertaba una fila con acceso = 0 por cada programa.
        Schema::create('usuario_programa_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('programa_estudio_id')->constrained('programas_estudio')->cascadeOnDelete();
            $table->boolean('acceso')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'programa_estudio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_programa_estudio');
    }
};
