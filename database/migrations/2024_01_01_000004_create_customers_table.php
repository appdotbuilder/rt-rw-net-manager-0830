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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('kontak');
            $table->string('username_pppoe')->unique();
            $table->string('password_pppoe');
            $table->foreignId('paket_id')->constrained('paket_internet')->onDelete('restrict');
            $table->string('ip_pool')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'suspended'])->default('nonaktif');
            $table->string('foto_ktp')->nullable()->comment('Path to KTP photo');
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index('nama');
            $table->index('username_pppoe');
            $table->index('status');
            $table->index('paket_id');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};