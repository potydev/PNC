<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jadwal_seminar_proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('jenis_sidang', 16)->default('Seminar Proposal');
            $table->foreignId('penguji_utama_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('penguji_pendamping_id')->constrained('dosen')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->foreignId('ruangan_sidang_id')->constrained('ruangan_sidang')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_seminar_proposal');
    }
};
