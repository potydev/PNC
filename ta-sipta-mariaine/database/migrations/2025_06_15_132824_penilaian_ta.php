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
        Schema::create('penilaian_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('rubrik_id')->constrained('rubrik_nilai')->onDelete('cascade');
            $table->foreignId('jadwal_sidang_tugas_akhir_id')->constrained('jadwal_sidang_tugas_akhir')->onDelete('cascade');
            $table->float('nilai')->nullable();
            $table->string('catatan_revisi')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'dosen_id', 'rubrik_id', 'jadwal_sidang_tugas_akhir_id'], 'penilaian_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_ta');
    }
};
