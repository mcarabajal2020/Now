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
        // Evitar errores de FK por cambios de esquema previos.
        // Si ya existe la tabla (aunque incompleta), la recreamos.
        Schema::dropIfExists('task_histories');

        Schema::create('task_histories', function (Blueprint $table) {
            $table->id();
            // MySQL: tasks.id es INT (unsigned). Alinear exactamente el tipo para el FK.
            $table->unsignedInteger('task_id')->comment('FK a tasks.id (INT unsigned)');

            // FK: omitida para evitar incompatibilidades de tipo/colación entre migraciones existentes.
            // (El modelo seguirá funcionando; la integridad referencial queda en la app.)

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('tipo', ['creado', 'asignacion', 'estado', 'comentario']);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_histories');
    }
};
