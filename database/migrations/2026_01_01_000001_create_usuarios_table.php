<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 15)->unique();          // antes idUsuario
            $table->string('password');                         // hash (bcrypt/argon), nunca texto plano
            $table->unsignedBigInteger('personal_id')->nullable(); // la FK se agrega en la migración de personal
            $table->enum('tipo_usuario', ['ADMINISTRADOR', 'COORDINADOR', 'ASISTENTE', 'CONSULTA'])
                  ->default('CONSULTA');
            $table->boolean('activo')->default(true);
            $table->rememberToken();

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();                              // reemplaza st_registro
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
