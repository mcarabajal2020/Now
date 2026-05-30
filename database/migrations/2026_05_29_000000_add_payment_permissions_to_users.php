<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('puede_autorizar')->default(false)->after('role_id');
            $table->boolean('puede_realizar_pago')->default(false)->after('puede_autorizar');
            $table->boolean('puede_realizar_transferencia')->default(false)->after('puede_realizar_pago');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['puede_autorizar', 'puede_realizar_pago', 'puede_realizar_transferencia']);
        });
    }
};
