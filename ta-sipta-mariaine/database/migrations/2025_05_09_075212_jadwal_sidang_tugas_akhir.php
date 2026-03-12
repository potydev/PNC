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
        Schema::create('jadwal_sidang_tugas_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->enum('jenis_sidang', ['Sidang Reguler', 'Sidang Ulang']);
            $table->foreignId('pembimbing_utama_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('pembimbing_pendamping_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('penguji_utama_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('penguji_pendamping_id')->constrained('dosen')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->foreignId('ruangan_sidang_id')->constrained('ruangan_sidang')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_sidang_tugas_akhir');
    }
};
