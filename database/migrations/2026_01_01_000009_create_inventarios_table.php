<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_estudio_id')->constrained('programas_estudio');
            $table->char('serie', 4);
            $table->string('numero', 8);

            $table->foreignId('sala_id')->constrained('salas');
            $table->string('nombre_sala', 60);                  // "foto" histórica

            $table->foreignId('coordinador_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->string('nombre_coordinador', 100)->nullable();
            $table->foreignId('asistente_t1_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->string('nombre_asistente_t1', 100)->nullable();
            $table->foreignId('asistente_t2_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->string('nombre_asistente_t2', 100)->nullable();

            $table->date('fecha_entrega')->nullable();
            $table->string('observacion', 500)->nullable();
            $table->enum('estado_inventario', ['ABIERTO', 'CERRADO', 'ANULADO'])->default('ABIERTO');

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['programa_estudio_id', 'serie', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
