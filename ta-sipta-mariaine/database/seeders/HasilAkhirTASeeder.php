<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HasilAkhirTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua kombinasi mahasiswa & jadwal dari penilaian_ta
        $combos = DB::table('penilaian_ta')
            ->select('mahasiswa_id', 'jadwal_sidang_tugas_akhir_id')
            ->distinct()
            ->get();

        foreach ($combos as $combo) {
            $mhsId    = $combo->mahasiswa_id;
            $jadwalId = $combo->jadwal_sidang_tugas_akhir_id;

            // Function untuk subtotal per jenis dosen, dibulatkan 1 desimal
            $subtotal = function (string $jenis) use ($mhsId, $jadwalId) {
                $sum = DB::table('penilaian_ta as p')
                    ->join('rubrik_nilai as r', 'p.rubrik_id', '=', 'r.id')
                    ->where('p.mahasiswa_id', $mhsId)
                    ->where('p.jadwal_sidang_tugas_akhir_id', $jadwalId)
                    ->where('r.jenis_dosen', $jenis)
                    ->selectRaw('SUM(p.nilai * r.persentase / 100) as s')
                    ->value('s') ?? 0;

                return round($sum, 1);
            };

            $nilaiPU    = $subtotal('Pembimbing Utama');
            $nilaiPP    = $subtotal('Pembimbing Pendamping');
            $nilaiPUjiU = $subtotal('Penguji Utama');
            $nilaiPUjiP = $subtotal('Penguji Pendamping');

            // Cari kaprodi
            $programId = DB::table('mahasiswa')
                ->where('id', $mhsId)
                ->value('program_studi_id');

            $kaprodiId = DB::table('dosen')
                ->where('jabatan', 'Koordinator Program Studi')
                ->where('program_studi_id', $programId)
                ->value('id');

            // Hitung total_akhir dan bulatkan 1 desimal
            $total = ($nilaiPU * 0.3)
                + ($nilaiPP * 0.3)
                + ($nilaiPUjiU * 0.2)
                + ($nilaiPUjiP * 0.2);

            DB::table('hasil_akhir_ta')->insert([
                'mahasiswa_id'                   => $mhsId,
                'jadwal_sidang_tugas_akhir_id'  => $jadwalId,
                'kaprodi_id'                    => $kaprodiId,
                'nilai_pembimbing_utama'        => $nilaiPU,
                'nilai_pembimbing_pendamping'   => $nilaiPP,
                'nilai_penguji_utama'           => $nilaiPUjiU,
                'nilai_penguji_pendamping'      => $nilaiPUjiP,
                'total_akhir'                   => round($total, 1),
                'created_at'                    => now(),
                'updated_at'                    => now(),
            ]);
        }
    }
}
