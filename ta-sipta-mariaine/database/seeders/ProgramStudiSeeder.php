<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('program_studi')->insert([
            [
                'kode_prodi' => '22001',
                'nama_prodi' => 'D3 Teknik Informatika'
            ],
            [
                'kode_prodi' => '22002',
                'nama_prodi' => 'D4 Rekayasa Keamanan Siber'
            ],
            [
                'kode_prodi' => '22003',
                'nama_prodi' => 'D4 Teknologi Rekayasa Multimedia'
            ],
            [
                'kode_prodi' => '22004',
                'nama_prodi' => 'D4 Akuntansi Lembaga Keuangan Syariah'
            ],
            [
                'kode_prodi' => '22005',
                'nama_prodi' => 'D4 Rekayasa Perangkat Lunak'
            ],
        ]);
    }
}
