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
        Schema::create('mikrotik_config', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Configuration name');
            $table->string('host')->comment('Mikrotik IP address');
            $table->integer('port')->default(8728)->comment('Mikrotik API port');
            $table->string('username')->comment('Mikrotik admin username');
            $table->string('password')->comment('Mikrotik admin password');
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamp('last_sync')->nullable()->comment('Last synchronization timestamp');
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('host');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_config');
    }
};