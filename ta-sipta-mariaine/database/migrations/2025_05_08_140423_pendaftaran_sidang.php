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
        Schema::create('pendaftaran_sidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->date('tanggal_pendaftaran');
            $table->string('file_tugas_akhir')->nullable(); //nullable buat uji coba
            $table->string('file_bebas_pinjaman_administrasi')->nullable();
            $table->string('file_slip_pembayaran_semester_akhir')->nullable();
            $table->string('file_transkip_sementara')->nullable();
            $table->string('file_bukti_pembayaran_sidang_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_sidang');
    }
};
