<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_programa_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->cascadeOnDelete();
            $table->foreignId('programa_estudio_id')->constrained('programas_estudio')->cascadeOnDelete();
            $table->string('cargo', 20); // COORDINADOR | DOCENTE | ASISTENTE
            $table->timestamps();

            $table->unique(['personal_id', 'programa_estudio_id', 'cargo'], 'personal_programa_cargo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_programa_estudio');
    }
};
