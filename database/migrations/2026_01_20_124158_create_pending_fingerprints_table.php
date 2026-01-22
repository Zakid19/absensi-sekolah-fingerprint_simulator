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
        Schema::create('pending_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->string('fingerprint_id')->unique();
            $table->string('device_ip')->nullable();
            $table->timestamp('detected_at');
            $table->boolean('is_mapped')->default(false);
             $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_fingerprints');
    }
};
