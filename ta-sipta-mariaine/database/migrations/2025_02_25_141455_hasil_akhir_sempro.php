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
        Schema::create('hasil_akhir_sempro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('jadwal_seminar_proposal_id')->constrained('jadwal_seminar_proposal')->onDelete('cascade');
            $table->float('nilai_penguji_utama')->nullable();
            $table->float('nilai_penguji_pendamping')->nullable();
            $table->float('total_akhir')->nullable();
            $table->enum('status_sidang', ['Lulus', 'Revisi', 'Ditolak'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_akhir_sempro');
    }
};
