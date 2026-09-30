<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Catálogos con la misma estructura, cada uno ligado a un programa de estudio. */
    private array $catalogos = ['grupos', 'marcas', 'modelos', 'tipos'];

    public function up(): void
    {
        foreach ($this->catalogos as $nombre) {
            Schema::create($nombre, function (Blueprint $table) {
                $table->id();
                $table->foreignId('programa_estudio_id')->constrained('programas_estudio');
                $table->string('nombre', 60);

                $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::create('estados', function (Blueprint $table) {
            $table->id();                                       // NUEVO, BUENO, REGULAR, MALO... (se carga con seeder)
            $table->string('nombre', 60)->unique();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados');

        foreach (array_reverse($this->catalogos) as $nombre) {
            Schema::dropIfExists($nombre);
        }
    }
};
