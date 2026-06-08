<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadir el nuevo valor 'finalizacion' al enum 'tipo'
        DB::statement("ALTER TABLE `task_histories` MODIFY `tipo` ENUM('creado','asignacion','estado','comentario','finalizacion') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir eliminando 'finalizacion' (asegúrate de no tener filas con ese valor antes de ejecutar down)
        DB::statement("ALTER TABLE `task_histories` MODIFY `tipo` ENUM('creado','asignacion','estado','comentario') NOT NULL");
    }
};
