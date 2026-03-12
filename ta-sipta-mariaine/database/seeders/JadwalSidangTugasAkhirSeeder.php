<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSidangTugasAkhirSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jadwal_sidang_tugas_akhir')->insert([
            [
                'mahasiswa_id' => 1, // Maria Ine
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 1, // Prih Diantono
                'pembimbing_pendamping_id' => 2, // Cahya Vikasari
                'penguji_utama_id' => 3, // Lutfi Syafirullah
                'penguji_pendamping_id' => 4, // Nur Wahyu
                'tanggal' => '2025-09-07',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1, // Lab. Jaringan Komputer
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2, // Puput Era
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 5, // Laura Sari
                'pembimbing_pendamping_id' => 6, // Annas Setiawan
                'penguji_utama_id' => 7, // Antonius Agung
                'penguji_pendamping_id' => 8, // Fajar Mahardika
                'tanggal' => '2025-09-07',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 2, // Lab. Sistem Informasi
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 3, // Rayhan Afrizal
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 9, // Dwi Novia
                'pembimbing_pendamping_id' => 10, // Isa Bahroni
                'penguji_utama_id' => 1, // Prih Diantono
                'penguji_pendamping_id' => 3, // Lutfi Syafirullah
                'tanggal' => '2025-09-08',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 4, // Yefta Charrand
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 2, // Cahya Vikasari
                'pembimbing_pendamping_id' => 4, // Nur Wahyu
                'penguji_utama_id' => 5, // Laura Sari
                'penguji_pendamping_id' => 6, // Annas Setiawan
                'tanggal' => '2025-09-08',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 5, // Gita Listiani
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 7, // Antonius Agung
                'pembimbing_pendamping_id' => 8, // Fajar Mahardika
                'penguji_utama_id' => 9, // Dwi Novia
                'penguji_pendamping_id' => 10, // Isa Bahroni
                'tanggal' => '2025-09-09',
                'waktu_mulai' => '13:00:00',
                'waktu_selesai' => '15:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 6, // Adisa Laras
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 3, // Lutfi Syafirullah
                'pembimbing_pendamping_id' => 4, // Nur Wahyu
                'penguji_utama_id' => 6, // Annas Setiawan
                'penguji_pendamping_id' => 1, // Prih Diantono
                'tanggal' => '2025-09-09',
                'waktu_mulai' => '13:00:00',
                'waktu_selesai' => '15:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 7, // Daniel Fajar
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 2, // Cahya Vikasari
                'pembimbing_pendamping_id' => 5, // Laura Sari
                'penguji_utama_id' => 8, // Fajar Mahardika
                'penguji_pendamping_id' => 9, // Dwi Novia
                'tanggal' => '2025-09-10',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 8, // Fardan Nur
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 4, // Nur Wahyu
                'pembimbing_pendamping_id' => 7, // Antonius Agung
                'penguji_utama_id' => 10, // Isa Bahroni
                'penguji_pendamping_id' => 1, // Prih Diantono
                'tanggal' => '2025-09-10',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 9, // Arif Nur
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 6, // Annas Setiawan
                'pembimbing_pendamping_id' => 8, // Fajar Mahardika
                'penguji_utama_id' => 2, // Cahya Vikasari
                'penguji_pendamping_id' => 3, // Lutfi Syafirullah
                'tanggal' => '2025-09-11',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 10, // Ratna Winingsih
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 9, // Dwi Novia
                'pembimbing_pendamping_id' => 3, // Lutfi Syafirullah
                'penguji_utama_id' => 5, // Laura Sari
                'penguji_pendamping_id' => 7, // Antonius Agung
                'tanggal' => '2025-09-11',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
