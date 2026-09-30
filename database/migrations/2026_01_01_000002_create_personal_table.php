<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('apellidos', 60);
            $table->string('nombres', 60);
            $table->char('dni', 8)->unique();
            $table->string('celular', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 150)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Se cierra el ciclo usuarios <-> personal
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign('personal_id')->references('id')->on('personal')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
        });

        Schema::dropIfExists('personal');
    }
};
