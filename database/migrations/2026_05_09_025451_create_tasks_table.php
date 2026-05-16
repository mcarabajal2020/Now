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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['Nuevo', 'En Proceso', 'Finalizado']);

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('ultima_modificacion')->nullable();

            $table->foreignId('usuario_solicita_id')->constrained('users');
            $table->foreignId('asignado_a_id')->nullable()->constrained('users');

            $table->timestamp('fecha_finalizacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
