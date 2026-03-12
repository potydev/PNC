<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LogbookBimbinganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswaIds = range(1, 7); // Maria Ine sampai Daniel Fajar

        foreach ($mahasiswaIds as $mahasiswaId) {
            $pendaftaranList = DB::table('pendaftaran_bimbingan')
                ->where('mahasiswa_id', $mahasiswaId)
                ->orderBy('id')
                ->limit(10)
                ->get();

            foreach ($pendaftaranList as $index => $pendaftaran) {
                $isLast = $index === count($pendaftaranList) - 1;

                DB::table('logbook_bimbingan')->insert([
                    'mahasiswa_id' => $mahasiswaId,
                    'pendaftaran_bimbingan_id' => $pendaftaran->id,
                    'permasalahan' => fake()->sentence(),
                    'file_bimbingan' => 'logbook/file_' . $pendaftaran->id . '.pdf',
                    'rekomendasi_utama' => $isLast ? 'Ya' : 'Tidak',
                    'rekomendasi_pendamping' => $isLast ? 'Ya' : 'Tidak',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
