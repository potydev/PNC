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
        Schema::create('rubrik_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
            $table->enum('jenis_dosen', ['Penguji Utama', 'Penguji Pendamping', 'Pembimbing Utama', 'Pembimbing Pendamping']);
            $table->string('kelompok', 50)->nullable();
            $table->string('kategori', 100);
            $table->integer('persentase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrik_nilai');
    }
};
