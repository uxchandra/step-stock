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
        Schema::create('sto_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('sto_event_id')->constrained('sto_events');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('stok_sistem');
            $table->integer('stok_aktual');
            $table->integer('selisih');
            $table->text('catatan')->nullable();
            $table->foreignId('scanned_by')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_items');
    }
};
