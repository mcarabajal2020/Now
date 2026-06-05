<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente_cbus', function (Blueprint $table) {
            $table->string('tipo_cbu', 50)->nullable()->after('banco');
        });

        // Si existían registros previos sin tipo, dejamos el valor en NULL.
        // La UI debe guardar el tipo_cbu al editar/crear desde Filament.

    }

    public function down(): void
    {
        Schema::table('cliente_cbus', function (Blueprint $table) {
            $table->dropColumn('tipo_cbu');
        });
    }
};

