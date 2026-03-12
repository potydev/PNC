<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mahasiswa')->insert([
            [
                'user_id' => 1,
                'nama_mahasiswa' => 'Maria Ine',
                'nim' => 220202001,
                'tempat_lahir' => 'Kebumen',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_maria.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'nama_mahasiswa' => 'Puput Era',
                'nim' => 220202002,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-02',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_puput.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'nama_mahasiswa' => 'Rayhan Afrizal',
                'nim' => 220202003,
                'tempat_lahir' => 'Banyumas',
                'tanggal_lahir' => '2000-03-03',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_rayhan.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'nama_mahasiswa' => 'Yefta Charrand',
                'nim' => 220202004,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-04',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_yefta.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'nama_mahasiswa' => 'Gita Listiani',
                'nim' => 220202005,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-05',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_gita.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6,
                'nama_mahasiswa' => 'Adisa Laras',
                'nim' => 220202006,
                'tempat_lahir' => 'Kebumen',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_adisa.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 7,
                'nama_mahasiswa' => 'Daniel Fajar',
                'nim' => 220202007,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-02',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_daniel.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 8,
                'nama_mahasiswa' => 'Fardan Nur',
                'nim' => 220202008,
                'tempat_lahir' => 'Banyumas',
                'tanggal_lahir' => '2000-03-03',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_fardan.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 9,
                'nama_mahasiswa' => 'Arif Nur',
                'nim' => 220202009,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-04',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 2,
                'tahun_ajaran_id' => 2,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_arif.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 10,
                'nama_mahasiswa' => 'Ratna Winingsih',
                'nim' => 220202010,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-05',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 2,
                'tahun_ajaran_id' => 2,
                'ttd_mahasiswa' => 'ttd_mahasiswa/ttd_ratna.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
