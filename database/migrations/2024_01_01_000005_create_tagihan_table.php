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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('periode', 7)->comment('Format: YYYY-MM');
            $table->decimal('jumlah', 12, 2);
            $table->date('jatuh_tempo');
            $table->enum('status', ['belum_lunas', 'lunas', 'terlambat'])->default('belum_lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('customer_id');
            $table->index('periode');
            $table->index('jatuh_tempo');
            $table->index('status');
            $table->unique(['customer_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};