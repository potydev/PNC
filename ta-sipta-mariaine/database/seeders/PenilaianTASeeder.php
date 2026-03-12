<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PenilaianTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        srand(2025);  // bebas pilih angka, tapi gunakan angka yang sama selamanya

        // Ambil semua mahasiswa 1–7 beserta program studinya
        $mahasiswas = DB::table('mahasiswa')
            ->whereIn('id', range(1, 7))
            ->select('id', 'program_studi_id')
            ->get();

        foreach ($mahasiswas as $mhs) {
            // Ambil semua jadwal tugas akhir milik mahasiswa ini
            $jadwals = DB::table('jadwal_sidang_tugas_akhir')
                ->where('mahasiswa_id', $mhs->id)
                ->get();

            foreach ($jadwals as $jadwal) {
                // Daftar dosen & perannya
                $dosenRoles = [
                    'Pembimbing Utama'     => $jadwal->pembimbing_utama_id,
                    'Pembimbing Pendamping' => $jadwal->pembimbing_pendamping_id,
                    'Penguji Utama'        => $jadwal->penguji_utama_id,
                    'Penguji Pendamping'   => $jadwal->penguji_pendamping_id,
                ];

                foreach ($dosenRoles as $jenisDosen => $dosenId) {
                    // Ambil rubrik sesuai program studi & jenis dosen
                    $rubriks = DB::table('rubrik_nilai')
                        ->where('program_studi_id', $mhs->program_studi_id)
                        ->where('jenis_dosen', $jenisDosen)
                        ->select('id')
                        ->get();

                    foreach ($rubriks as $rubrik) {
                        DB::table('penilaian_ta')->insert([
                            'mahasiswa_id'                  => $mhs->id,
                            'dosen_id'                      => $dosenId,
                            'rubrik_id'                     => $rubrik->id,
                            'jadwal_sidang_tugas_akhir_id' => $jadwal->id,
                            // acak integer 60–100
                            'nilai'                         => rand(60, 100),
                            'catatan_revisi'                => null,
                            'created_at'                    => now(),
                            'updated_at'                    => now(),
                        ]);
                    }
                }
            }
        }
    }
}
