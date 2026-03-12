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
        Schema::create('logbook_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('pendaftaran_bimbingan_id')->constrained('pendaftaran_bimbingan')->onDelete('cascade');
            $table->string('permasalahan')->nullable();
            $table->string('file_bimbingan');
            $table->enum('rekomendasi_utama', ['Ya', 'Tidak'])->default('Tidak');
            $table->enum('rekomendasi_pendamping', ['Ya', 'Tidak'])->default('Tidak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_bimbingan');
    }
};
