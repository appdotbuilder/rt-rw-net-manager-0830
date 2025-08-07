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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah', 12, 2);
            $table->enum('metode', ['tunai', 'transfer', 'e_wallet', 'lainnya'])->default('tunai');
            $table->string('bukti')->nullable()->comment('Path to payment proof image');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('tagihan_id');
            $table->index('tanggal_bayar');
            $table->index('metode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};