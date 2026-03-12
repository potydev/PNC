<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahun_ajaran')->insert([
            [
                'tahun_ajaran' => '2020/2021',
            ],
            [
                'tahun_ajaran' => '2021/2022',
            ],
            [
                'tahun_ajaran' => '2022/2023',
            ],
            [
                'tahun_ajaran' => '2023/2024',
            ],
            [
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'tahun_ajaran' => '2025/2026',
            ],
        ]);
    }
}
