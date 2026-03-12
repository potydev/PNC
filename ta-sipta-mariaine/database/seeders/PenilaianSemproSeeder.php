<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PenilaianSemproSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penilaian_sempro')->insert([
            [
                'mahasiswa_id' => 1, // Maria Ine
                'dosen_id' => 1, // Prih Diantono (penguji utama)
                'jadwal_seminar_proposal_id' => 1,
                'nilai' => 85,
                'catatan_revisi' => 'Perbaiki struktur latar belakang.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 1,
                'dosen_id' => 2, // Cahya Vikasari (penguji pendamping)
                'jadwal_seminar_proposal_id' => 1,
                'nilai' => 88,
                'catatan_revisi' => 'Referensi perlu diperkuat.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2, // Puput Era
                'dosen_id' => 3, // Lutfi Syafirullah
                'jadwal_seminar_proposal_id' => 2,
                'nilai' => 82,
                'catatan_revisi' => 'Kurangi kesalahan penulisan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2,
                'dosen_id' => 4, // Nur Wahyu
                'jadwal_seminar_proposal_id' => 2,
                'nilai' => 86,
                'catatan_revisi' => 'Tambahkan studi pustaka yang relevan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 3, // Rayhan Afrizal
                'dosen_id' => 5, // Laura Sari
                'jadwal_seminar_proposal_id' => 3,
                'nilai' => 84,
                'catatan_revisi' => 'Penulisan tujuan penelitian perlu dilengkapi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 3,
                'dosen_id' => 6, // Annas Setiawan
                'jadwal_seminar_proposal_id' => 3,
                'nilai' => 83,
                'catatan_revisi' => 'Metodologi perlu diperjelas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 4, // Yefta Charrand
                'dosen_id' => 7, // Antonius Agung
                'jadwal_seminar_proposal_id' => 4,
                'nilai' => 80,
                'catatan_revisi' => 'Format tabel belum rapi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 4,
                'dosen_id' => 8, // Fajar Mahardika
                'jadwal_seminar_proposal_id' => 4,
                'nilai' => 82,
                'catatan_revisi' => 'Cek ulang rumusan masalah.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 5, // Gita Listiani
                'dosen_id' => 9, // Dwi Novia
                'jadwal_seminar_proposal_id' => 5,
                'nilai' => 87,
                'catatan_revisi' => 'Gaya bahasa di bab 2 perlu disesuaikan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 5,
                'dosen_id' => 10, // Isa Bahroni
                'jadwal_seminar_proposal_id' => 5,
                'nilai' => 85,
                'catatan_revisi' => 'Jelaskan referensi pada landasan teori.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 6, // Adisa Laras
                'dosen_id' => 2, // Cahya Vikasari
                'jadwal_seminar_proposal_id' => 6,
                'nilai' => 86,
                'catatan_revisi' => 'Abstrak terlalu panjang.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 6,
                'dosen_id' => 3, // Lutfi Syafirullah
                'jadwal_seminar_proposal_id' => 6,
                'nilai' => 88,
                'catatan_revisi' => 'Perbaiki format daftar pustaka.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
