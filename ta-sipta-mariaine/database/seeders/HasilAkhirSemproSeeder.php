<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class HasilAkhirSemproSeeder extends Seeder
{

    public function run(): void
    {
        $data = [
            ['mahasiswa_id' => 1, 'jadwal_seminar_proposal_id' => 1, 'nilai_penguji_utama' => 85, 'nilai_penguji_pendamping' => 87],
            ['mahasiswa_id' => 2, 'jadwal_seminar_proposal_id' => 2, 'nilai_penguji_utama' => 80, 'nilai_penguji_pendamping' => 82],
            ['mahasiswa_id' => 3, 'jadwal_seminar_proposal_id' => 3, 'nilai_penguji_utama' => 78, 'nilai_penguji_pendamping' => 81],
            ['mahasiswa_id' => 4, 'jadwal_seminar_proposal_id' => 4, 'nilai_penguji_utama' => 84, 'nilai_penguji_pendamping' => 85],
            ['mahasiswa_id' => 5, 'jadwal_seminar_proposal_id' => 5, 'nilai_penguji_utama' => 83, 'nilai_penguji_pendamping' => 80],
            ['mahasiswa_id' => 6, 'jadwal_seminar_proposal_id' => 6, 'nilai_penguji_utama' => 89, 'nilai_penguji_pendamping' => 90],
            ['mahasiswa_id' => 7, 'jadwal_seminar_proposal_id' => 7, 'nilai_penguji_utama' => 84, 'nilai_penguji_pendamping' => 86],
            ['mahasiswa_id' => 8, 'jadwal_seminar_proposal_id' => 8, 'nilai_penguji_utama' => 83, 'nilai_penguji_pendamping' => 82],
            ['mahasiswa_id' => 9, 'jadwal_seminar_proposal_id' => 9, 'nilai_penguji_utama' => 85, 'nilai_penguji_pendamping' => 86],
            ['mahasiswa_id' => 10, 'jadwal_seminar_proposal_id' => 10, 'nilai_penguji_utama' => 87, 'nilai_penguji_pendamping' => 88],
        ];

        foreach ($data as $item) {
            $mahasiswaId = $item['mahasiswa_id'];
            $jadwalId = $item['jadwal_seminar_proposal_id'];
            $nilai1 = $item['nilai_penguji_utama'];
            $nilai2 = $item['nilai_penguji_pendamping'];
            $total = ($nilai1 + $nilai2) / 2;

            // Cek apakah ada catatan revisi
            $punyaCatatanRevisi = DB::table('penilaian_sempro')
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('jadwal_seminar_proposal_id', $jadwalId)
                ->whereNotNull('catatan_revisi')
                ->exists();

            // Tentukan status_sidang
            if (!$punyaCatatanRevisi) {
                $status = 'Lulus';
            } else {
                if ($total < 50) {
                    $status = 'Ditolak';
                } elseif ($total <= 100) {
                    $status = 'Revisi';
                } else {
                    $status = 'Tidak Valid';
                }
            }

            DB::table('hasil_akhir_sempro')->insert([
                'mahasiswa_id' => $mahasiswaId,
                'jadwal_seminar_proposal_id' => $jadwalId,
                'nilai_penguji_utama' => $nilai1,
                'nilai_penguji_pendamping' => $nilai2,
                'total_akhir' => $total,
                'status_sidang' => $status,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    // public function run(): void
    // {
    //     $data = [
    //         [
    //             'mahasiswa_id' => 1,
    //             'jadwal_seminar_proposal_id' => 1,
    //             'nilai_penguji_utama' => 85,
    //             'nilai_penguji_pendamping' => 87,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 2,
    //             'jadwal_seminar_proposal_id' => 2,
    //             'nilai_penguji_utama' => 80,
    //             'nilai_penguji_pendamping' => 82,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 3,
    //             'jadwal_seminar_proposal_id' => 3,
    //             'nilai_penguji_utama' => 78,
    //             'nilai_penguji_pendamping' => 81,
    //             'status_sidang' => 'Revisi',
    //         ],
    //         [
    //             'mahasiswa_id' => 4,
    //             'jadwal_seminar_proposal_id' => 4,
    //             'nilai_penguji_utama' => 84,
    //             'nilai_penguji_pendamping' => 85,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 5,
    //             'jadwal_seminar_proposal_id' => 5,
    //             'nilai_penguji_utama' => 83,
    //             'nilai_penguji_pendamping' => 80,
    //             'status_sidang' => 'Revisi',
    //         ],
    //         [
    //             'mahasiswa_id' => 6,
    //             'jadwal_seminar_proposal_id' => 6,
    //             'nilai_penguji_utama' => 89,
    //             'nilai_penguji_pendamping' => 90,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 7,
    //             'jadwal_seminar_proposal_id' => 7,
    //             'nilai_penguji_utama' => 84,
    //             'nilai_penguji_pendamping' => 86,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 8,
    //             'jadwal_seminar_proposal_id' => 8,
    //             'nilai_penguji_utama' => 83,
    //             'nilai_penguji_pendamping' => 82,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 9,
    //             'jadwal_seminar_proposal_id' => 9,
    //             'nilai_penguji_utama' => 85,
    //             'nilai_penguji_pendamping' => 86,
    //             'status_sidang' => 'Lulus',
    //         ],
    //         [
    //             'mahasiswa_id' => 10,
    //             'jadwal_seminar_proposal_id' => 10,
    //             'nilai_penguji_utama' => 87,
    //             'nilai_penguji_pendamping' => 88,
    //             'status_sidang' => 'Lulus',
    //         ],
    //     ];

    //     foreach ($data as $item) {
    //         DB::table('hasil_akhir_sempro')->insert([
    //             'mahasiswa_id' => $item['mahasiswa_id'],
    //             'jadwal_seminar_proposal_id' => $item['jadwal_seminar_proposal_id'],
    //             'nilai_penguji_utama' => $item['nilai_penguji_utama'],
    //             'nilai_penguji_pendamping' => $item['nilai_penguji_pendamping'],
    //             'total_akhir' => ($item['nilai_penguji_utama'] + $item['nilai_penguji_pendamping']) / 2,
    //             'status_sidang' => $item['status_sidang'],
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         ]);
    //     }
    // }
}
