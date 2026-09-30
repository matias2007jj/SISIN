<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programas_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones');
            $table->string('nombre', 100);
            $table->string('abreviatura', 30)->nullable();
            $table->foreignId('coordinador_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->foreignId('asistente_t1_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->foreignId('asistente_t2_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->string('observacion', 150)->nullable();
            $table->boolean('activo')->default(true);           // antes st_programaEstudio 'ACTIVO'

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas_estudio');
    }
};
