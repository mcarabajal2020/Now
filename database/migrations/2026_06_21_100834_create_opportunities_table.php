<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->index();
            $table->text('descripcion')->nullable();

            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('monto_estimado', 15, 2)->nullable();
            $table->unsignedTinyInteger('probabilidad')->default(10);
            $table->date('fecha_esperada_cierre')->nullable();

            $table->string('etapa')->default('prospeccion')->index();
            $table->string('origen')->nullable();
            $table->string('fuente')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('ganada_at')->nullable();
            $table->timestamp('perdida_at')->nullable();
            $table->text('motivo_perdida')->nullable();

            $table->timestamps();

            $table->index(['etapa', 'user_id']);
            $table->index(['cliente_id', 'etapa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
