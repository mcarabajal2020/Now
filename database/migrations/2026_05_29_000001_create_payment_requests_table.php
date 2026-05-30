<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('cliente_cbu_id')->nullable()->constrained('cliente_cbus')->nullOnDelete();
            $table->string('numero_cuenta')->nullable();
            $table->string('nombre_cuenta')->nullable();
            $table->decimal('monto', 15, 2);
            $table->date('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado')->default('pendiente_autorizacion');

            $table->foreignId('solicitante_id')->constrained('users');
            $table->foreignId('autorizado_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('autorizado_at')->nullable();

            $table->foreignId('pagado_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pagado_at')->nullable();

            $table->foreignId('transferido_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transferido_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
