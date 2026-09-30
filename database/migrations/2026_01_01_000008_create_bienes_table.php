<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bienes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained('salas');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->string('cbi', 10)->unique();                // código patrimonial
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->string('descripcion', 100);
            $table->foreignId('marca_id')->nullable()->constrained('marcas');   // nullable: bienes "sin marca"
            $table->foreignId('modelo_id')->nullable()->constrained('modelos'); // nullable: bienes "sin modelo"
            $table->string('serie_medida', 100)->nullable();
            $table->foreignId('estado_id')->constrained('estados');
            $table->string('otras_especificaciones', 100)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bienes');
    }
};
