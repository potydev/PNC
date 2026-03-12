<?php

namespace Database\Seeders;

use App\Models\HasilAkhirTA;
use App\Models\CatatanRevisiTA;
use App\Models\HasilSidang;
use App\Models\RiwayatSidang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HasilSidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hasilAkhirs = HasilAkhirTA::whereNotNull('total_akhir')->get();

        foreach ($hasilAkhirs as $ha) {
            $mahasiswaId = $ha->mahasiswa_id;
            $jadwalId    = $ha->jadwal_sidang_tugas_akhir_id;

            // Cek apakah mahasiswa punya catatan revisi
            $punyaRevisi = CatatanRevisiTA::where('mahasiswa_id', $mahasiswaId)
                ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
                ->exists();

            $rata2 = ($ha->nilai_penguji_utama + $ha->nilai_penguji_pendamping) / 2;

            if (! $punyaRevisi) {
                $statusSidang = 'Lulus';
            } else {
                $statusSidang = $rata2 < 50 ? 'Sidang Ulang' : 'Revisi';
            }

            $hasilSidang = HasilSidang::updateOrCreate(
                ['mahasiswa_id' => $mahasiswaId],
                [
                    'status_kelulusan' => $statusSidang,
                    'tahun_lulus'      => in_array($statusSidang, ['Lulus', 'Revisi'])
                        ? now()->format('Y')
                        : null,
                    'file_revisi'      => null,
                    'tanggal_revisi'   => null,
                ]
            );

            $exists = RiwayatSidang::where('hasil_sidang_id', $hasilSidang->id)
                ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
                ->exists();

            if (! $exists) {
                RiwayatSidang::create([
                    'hasil_sidang_id'              => $hasilSidang->id,
                    'jadwal_sidang_tugas_akhir_id' => $jadwalId,
                    'status_sidang'                => $statusSidang,
                ]);

                // Update status last
                $riwayatTerbaru = RiwayatSidang::where('hasil_sidang_id', $hasilSidang->id)
                    ->latest()
                    ->first();

                if ($riwayatTerbaru) {
                    $hasilSidang->update([
                        'status_kelulusan' => $riwayatTerbaru->status_sidang,
                    ]);
                }
            }
        }
    }
}
