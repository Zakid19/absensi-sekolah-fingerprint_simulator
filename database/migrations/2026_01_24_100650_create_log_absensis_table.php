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
        Schema::create('log_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->foreignId('attendance_id')
                ->nullable()
                ->constrained('absensi')
                ->nullOnDelete();

            $table->dateTime('waktu_scan');

            $table->enum('jenis', ['masuk', 'pulang'])->default('masuk');

            $table->enum('status', ['success', 'duplicate', 'rejected']);

            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_absensis');
    }
};
