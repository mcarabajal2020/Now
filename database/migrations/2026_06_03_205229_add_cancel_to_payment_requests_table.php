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
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->timestamp('cancelado_at')->nullable()->after('transferido_at');
            $table->foreignId('cancelado_por_id')->nullable()->after('cancelado_at')->constrained('users')->nullOnDelete();
            $table->text('cancelacion_observaciones')->nullable()->after('cancelado_por_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn('cancelacion_observaciones');
            $table->dropConstrainedForeignId('cancelado_por_id');
            $table->dropColumn('cancelado_at');
        });
    }
};
