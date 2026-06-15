<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_cierres', function (Blueprint $table) {
            if (! Schema::hasColumn('tipo_cierres', 'nombre')) {
                $table->string('nombre', 255)->after('id')->nullable(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tipo_cierres', function (Blueprint $table) {
            if (Schema::hasColumn('tipo_cierres', 'nombre')) {
                $table->dropColumn('nombre');
            }
        });
    }
};
