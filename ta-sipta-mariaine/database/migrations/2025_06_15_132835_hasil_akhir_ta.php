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
        Schema::create('hasil_akhir_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('jadwal_sidang_tugas_akhir_id')->constrained('jadwal_sidang_tugas_akhir')->onDelete('cascade');
            $table->foreignId('kaprodi_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->float('nilai_pembimbing_utama')->nullable();
            $table->float('nilai_pembimbing_pendamping')->nullable();
            $table->float('nilai_penguji_utama')->nullable();
            $table->float('nilai_penguji_pendamping')->nullable();
            $table->float('total_akhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_akhir_ta');
    }
};
