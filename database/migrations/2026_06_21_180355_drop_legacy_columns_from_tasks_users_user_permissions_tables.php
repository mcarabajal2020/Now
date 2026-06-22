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
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);
            $table->dropColumn('permission_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }
};
