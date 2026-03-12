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
        Schema::create('riwayat_sidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_sidang_id')->constrained('hasil_sidang')->onDelete('cascade');
            $table->foreignId('jadwal_sidang_tugas_akhir_id')->constrained('jadwal_sidang_tugas_akhir')->onDelete('cascade');
            $table->string('status_sidang', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_sidang');
    }
};
