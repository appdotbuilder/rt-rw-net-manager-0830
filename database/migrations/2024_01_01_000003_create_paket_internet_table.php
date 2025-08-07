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
        Schema::create('paket_internet', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->decimal('harga', 12, 2);
            $table->string('bandwidth')->comment('Bandwidth allocation (e.g., 10Mbps)');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            
            $table->index('nama_paket');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_internet');
    }
};