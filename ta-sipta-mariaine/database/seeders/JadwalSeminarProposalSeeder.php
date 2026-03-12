<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeminarProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jadwal_seminar_proposal')->insert([
            [
                'mahasiswa_id' => 1, // Maria Ine
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 1, // Prih Diantono
                'penguji_pendamping_id' => 2, // Cahya Vikasari
                'tanggal' => '2025-08-01',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1, // Lab. Jaringan Komputer
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2, // Puput Era
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 3, // Lutfi Syafirullah
                'penguji_pendamping_id' => 4, // Nur Wahyu
                'tanggal' => '2025-08-01',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 2, // Lab. Sistem Informasi
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 3, // Rayhan Afrizal
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 5, // Laura Sari
                'penguji_pendamping_id' => 6, // Annas Setiawan
                'tanggal' => '2025-08-02',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 4, // Yefta Charrand
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 7, // Antonius Agung
                'penguji_pendamping_id' => 8, // Fajar Mahardika
                'tanggal' => '2025-08-02',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 5, // Gita Listiani
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 9, // Dwi Novia
                'penguji_pendamping_id' => 10, // Isa Bahroni
                'tanggal' => '2025-08-03',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 6, // Adisa Laras
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 2, // Cahya Vikasari
                'penguji_pendamping_id' => 3, // Lutfi Syafirullah
                'tanggal' => '2025-08-03',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 7, // Daniel Fajar
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 4, // Nur Wahyu
                'penguji_pendamping_id' => 5, // Laura Sari
                'tanggal' => '2025-08-04',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 8, // Fardan Nur
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 6, // Annas Setiawan
                'penguji_pendamping_id' => 1, // Prih Diantono
                'tanggal' => '2025-08-04',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 9, // Arif Nur
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 8, // Fajar Mahardika
                'penguji_pendamping_id' => 9, // Dwi Novia
                'tanggal' => '2025-08-05',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 3, // Lab. Pemrograman Dasar
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 10, // Ratna Winingsih
                'jenis_sidang' => 'Seminar Proposal',
                'penguji_utama_id' => 6, // Annas Setiawan
                'penguji_pendamping_id' => 1, // Prih Diantono
                'tanggal' => '2025-08-06',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
