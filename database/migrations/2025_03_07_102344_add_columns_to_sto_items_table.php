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
        Schema::table('sto_items', function (Blueprint $table) {
            $table->timestamp('waktu_scan')->nullable();
            $table->enum('status', ['open', 'close'])->default('open')->after('selisih');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sto_items', function (Blueprint $table) {
            $table->dropColumn(['waktu_scan', 'status']);
        });
    }
};
