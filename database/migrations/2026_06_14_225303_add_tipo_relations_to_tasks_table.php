<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Relación con tipo tarea
            $table->foreignId('tipo_tarea_id')
                ->nullable()
                ->constrained('tipo_tareas')
                ->nullOnDelete();

            // Relación con tipo cierre
            $table->foreignId('tipo_cierre_id')
                ->nullable()
                ->constrained('tipo_cierres')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_cierre_id');
            $table->dropConstrainedForeignId('tipo_tarea_id');
        });
    }
};
