<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('tipo_uso', 50)->default('uso interno');

            $table->string('tipo_tarea', 80)->nullable();

            $table->string('prioridad', 20)->default('prioridad alta');

            // Opcional: referencia externa al cliente (solo cuando tipo_uso = uso externo)
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cliente_id');
            $table->dropColumn('prioridad');
            $table->dropColumn('tipo_tarea');
            $table->dropColumn('tipo_uso');
            $table->dropColumn('cliente_id');
        });
    }
};

