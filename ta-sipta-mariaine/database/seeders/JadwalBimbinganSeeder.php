<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JadwalBimbinganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kuotas = [4, 5, 6];
        $jamMulai = ['08:00:00', '09:00:00', '10:00:00', '13:00:00', '14:00:00', '15:00:00'];
        $tanggal = collect(range(10, 20))->map(function ($day) {
            return "2025-08-" . str_pad($day, 2, '0', STR_PAD_LEFT);
        })->toArray();

        foreach (range(1, 10) as $dosenId) {
            for ($i = 0; $i < 5; $i++) {
                DB::table('jadwal_bimbingan')->insert([
                    'dosen_id' => $dosenId,
                    'tanggal' => $tanggal[array_rand($tanggal)],
                    'waktu' => $jamMulai[array_rand($jamMulai)],
                    'kuota' => $kuotas[array_rand($kuotas)],
                    'status' => 'Terjadwal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // DB::table('jadwal_bimbingan')->insert([
        //     [
        //         'dosen_id' => 1,
        //         'tanggal' => '2025-07-01',
        //         'waktu' => '08:00:00',
        //         'kuota' => 5,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 1,
        //         'tanggal' => '2025-07-03',
        //         'waktu' => '10:00:00',
        //         'kuota' => 12,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 1,
        //         'tanggal' => '2025-07-06',
        //         'waktu' => '14:00:00',
        //         'kuota' => 8,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 1,
        //         'tanggal' => '2025-07-10',
        //         'waktu' => '10:00:00',
        //         'kuota' => 10,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 1,
        //         'tanggal' => '2025-07-12',
        //         'waktu' => '14:00:00',
        //         'kuota' => 4,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 2,
        //         'tanggal' => '2025-07-05',
        //         'waktu' => '07:00:00',
        //         'kuota' => 6,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 2,
        //         'tanggal' => '2025-07-06',
        //         'waktu' => '09:00:00',
        //         'kuota' => 6,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 2,
        //         'tanggal' => '2025-07-08',
        //         'waktu' => '07:00:00',
        //         'kuota' => 8,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 2,
        //         'tanggal' => '2025-07-12',
        //         'waktu' => '09:00:00',
        //         'kuota' => 10,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'dosen_id' => 2,
        //         'tanggal' => '2025-07-15',
        //         'waktu' => '07:00:00',
        //         'kuota' => 4,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);
    }
}
