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
        Schema::create('hasil_sidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('status_kelulusan', 20)->nullable();
            $table->string('tahun_lulus', 4)->nullable();
            $table->string('file_revisi')->nullable();
            $table->date('tanggal_revisi')->nullable();
            $table->enum('kelengkapan_yudisium', ['Lengkap', 'Belum Lengkap'])->default('Belum Lengkap');
            $table->timestamps();
            $table->unique('mahasiswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_sidang');
    }
};
