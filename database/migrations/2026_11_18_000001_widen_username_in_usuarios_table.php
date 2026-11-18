<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // De 15 a 100 caracteres: ahora el usuario puede ser un correo (institucion@gmail.edu.pe)
        DB::statement('ALTER TABLE usuarios MODIFY username VARCHAR(100) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE usuarios MODIFY username VARCHAR(15) NOT NULL');
    }
};
