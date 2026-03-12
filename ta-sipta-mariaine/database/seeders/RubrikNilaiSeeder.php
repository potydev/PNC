<?php

namespace Database\Seeders;

use App\Models\RubrikNilai;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RubrikNilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 1 - Latar Belakang, Tujuan, Batasan Masalah',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 2 - Landasan Teori',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 3 - Data Pendukung dan Desain',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 4 - Manual Book',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 5 - Kesimpulan, Saran, Lampiran',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => null,
                'kategori' => 'Presentasi',
                'persentase' => 20,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => null,
                'kategori' => 'Penguasaan Materi',
                'persentase' => 40,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Utama',
                'kelompok' => null,
                'kategori' => 'Hasil Tugas Akhir',
                'persentase' => 30,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 1 - Latar Belakang, Tujuan, Batasan Masalah',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 2 - Landasan Teori',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 3 - Data Pendukung dan Desain',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 4 - Manual Book',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => 'Tata Tulis',
                'kategori' => 'BAB 5 - Kesimpulan, Saran, Lampiran',
                'persentase' => 2,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => null,
                'kategori' => 'Presentasi',
                'persentase' => 20,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => null,
                'kategori' => 'Penguasaan Materi',
                'persentase' => 40,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Penguji Pendamping',
                'kelompok' => null,
                'kategori' => 'Hasil Tugas Akhir',
                'persentase' => 30,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Pembimbing Utama',
                'kelompok' => null,
                'kategori' => 'Keaktifan Bimbingan',
                'persentase' => 40,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Pembimbing Utama',
                'kelompok' => null,
                'kategori' => 'Keaktifan Pembuatan Sistem',
                'persentase' => 40,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Pembimbing Utama',
                'kelompok' => null,
                'kategori' => 'Presentasi',
                'persentase' => 20,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Pembimbing Pendamping',
                'kelompok' => null,
                'kategori' => 'Keaktifan Bimbingan',
                'persentase' => 40,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Pembimbing Pendamping',
                'kelompok' => null,
                'kategori' => 'Keaktifan Pembuatan Sistem',
                'persentase' => 40,
            ],
            [
                'program_studi_id' => 1,
                'jenis_dosen' => 'Pembimbing Pendamping',
                'kelompok' => null,
                'kategori' => 'Presentasi',
                'persentase' => 20,
            ],
        ];

        RubrikNilai::insert($data);
    }
}
