<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CatatanRevisiTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua jadwal sidang tugas akhir
        $jadwals = DB::table('jadwal_sidang_tugas_akhir')->get();

        foreach ($jadwals as $jadwal) {
            $mahasiswaId = $jadwal->mahasiswa_id;

            // Array dosen untuk jadwal ini
            $dosenIds = [
                $jadwal->pembimbing_utama_id,
                $jadwal->pembimbing_pendamping_id,
                $jadwal->penguji_utama_id,
                $jadwal->penguji_pendamping_id,
            ];

            foreach ($dosenIds as $dosenId) {
                DB::table('catatan_revisi_ta')->insert([
                    'mahasiswa_id'                   => $mahasiswaId,
                    'dosen_id'                       => $dosenId,
                    'jadwal_sidang_tugas_akhir_id'   => $jadwal->id,
                    'catatan_revisi'                 => "Catatan revisi untuk mahasiswa {$mahasiswaId} dari dosen {$dosenId}.",
                    'created_at'                     => now(),
                    'updated_at'                     => now(),
                ]);
            }
        }
    }
}
