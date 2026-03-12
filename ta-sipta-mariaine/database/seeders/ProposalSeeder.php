<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('proposal')->insert([
            [
                'mahasiswa_id' => 1,
                'judul_proposal' => 'Sistem Informasi Pengelolaan Tugas Akhir',
                'file_proposal' => 'proposal/file_maria.pdf',
                'revisi_judul_proposal' => 'Sistem Informasi Pengelolaan Tugas Akhir JKB',
                'revisi_file_proposal' => 'proposal/revisi_maria.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2,
                'judul_proposal' => 'Penerapan IoT pada Smart Home',
                'file_proposal' => 'proposal/file_puput.pdf',
                'revisi_judul_proposal' => 'Penerapan IoT dalam Otomatisasi Rumah',
                'revisi_file_proposal' => 'proposal/revisi_puput.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 3,
                'judul_proposal' => 'Sistem Monitoring Kualitas Air Berbasis Sensor',
                'file_proposal' => 'proposal/file_rayhan.pdf',
                'revisi_judul_proposal' => null,
                'revisi_file_proposal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 4,
                'judul_proposal' => 'Aplikasi Pembelajaran Bahasa Isyarat',
                'file_proposal' => 'proposal/file_yefta.pdf',
                'revisi_judul_proposal' => null,
                'revisi_file_proposal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 5,
                'judul_proposal' => 'Website E-Commerce Produk Lokal',
                'file_proposal' => 'proposal/file_gita.pdf',
                'revisi_judul_proposal' => 'Aplikasi E-Commerce Produk UMKM',
                'revisi_file_proposal' => 'proposal/revisi_gita.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 6,
                'judul_proposal' => 'Aplikasi Manajemen Keuangan Pribadi',
                'file_proposal' => 'proposal/file_adisa.pdf',
                'revisi_judul_proposal' => null,
                'revisi_file_proposal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 7,
                'judul_proposal' => 'Sistem Informasi Klinik Gigi',
                'file_proposal' => 'proposal/file_daniel.pdf',
                'revisi_judul_proposal' => null,
                'revisi_file_proposal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 8,
                'judul_proposal' => 'Aplikasi Reminder Obat Berbasis Android',
                'file_proposal' => 'proposal/file_fardan.pdf',
                'revisi_judul_proposal' => 'Reminder Minum Obat Android',
                'revisi_file_proposal' => 'proposal/revisi_fardan.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 9,
                'judul_proposal' => 'Sistem Pendukung Keputusan Pemilihan Laptop',
                'file_proposal' => 'proposal/file_arif.pdf',
                'revisi_judul_proposal' => null,
                'revisi_file_proposal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
