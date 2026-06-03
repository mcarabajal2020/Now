<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->decimal('total_pagado', 15, 2)->default(0);
            $table->text('observaciones_pago')->nullable();
            // Guardamos múltiples imágenes como JSON array de strings (rutas/URLs)
            $table->json('imagenes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn(['total_pagado', 'observaciones_pago', 'imagenes']);
        });
    }
};

