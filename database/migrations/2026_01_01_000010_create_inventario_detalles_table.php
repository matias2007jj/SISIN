<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cada fila es una "foto" del bien al momento del inventario:
        // si luego cambia la marca o el estado en el catálogo, el historial no se altera.
        Schema::create('inventario_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios')->cascadeOnDelete();
            $table->foreignId('bien_id')->constrained('bienes');

            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->string('nombre_grupo', 60)->nullable();
            $table->string('nro_drelm', 10)->nullable();
            $table->string('cbi', 10);
            $table->foreignId('tipo_id')->nullable()->constrained('tipos')->nullOnDelete();
            $table->string('nombre_tipo', 60)->nullable();
            $table->string('descripcion', 100);
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
            $table->string('nombre_marca', 60)->nullable();
            $table->foreignId('modelo_id')->nullable()->constrained('modelos')->nullOnDelete();
            $table->string('nombre_modelo', 60)->nullable();
            $table->string('serie_medida', 100)->nullable();
            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->string('nombre_estado', 60)->nullable();
            $table->string('otras_especificaciones', 100)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['inventario_id', 'bien_id']);       // un bien no se repite en el mismo inventario
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_detalles');
    }
};
