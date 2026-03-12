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
        Schema::create('catatan_revisi_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('jadwal_sidang_tugas_akhir_id')->constrained('jadwal_sidang_tugas_akhir')->onDelete('cascade');
            $table->string('catatan_revisi')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'dosen_id', 'jadwal_sidang_tugas_akhir_id'], 'catatan_revisi_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_revisi_ta');
    }
};
