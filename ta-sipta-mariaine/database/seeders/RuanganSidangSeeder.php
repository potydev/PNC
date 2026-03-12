<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ruangan_sidang')->insert([
            [
                'tempat' => 'Lantai 1 GTIL',
                'nama_ruangan' => 'Lab. Jaringan Komputer'
            ],
            [
                'tempat' => 'Lantai 2 GTIL',
                'nama_ruangan' => 'Lab. Sistem Informasi'
            ],
            [
                'tempat' => 'Lantai 2 GTIL',
                'nama_ruangan' => 'Lab. Pemrograman Dasar'
            ],
            [
                'tempat' => 'Lantai 1 GTIL',
                'nama_ruangan' => 'Lab. Keamanan Jaringan'
            ],
            [
                'tempat' => 'Lantai 3 GTIL',
                'nama_ruangan' => 'Lab. Multimedia'
            ],
            [
                'tempat' => 'Lantai 3 GTIL',
                'nama_ruangan' => 'Lab. Desain Komunikasi Visual'
            ],
        ]);
    }
}
