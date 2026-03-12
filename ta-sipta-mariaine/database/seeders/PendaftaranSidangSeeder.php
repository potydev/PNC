<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PendaftaranSidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tanggal = collect(range(21, 27))->map(function ($day) {
            return "2025-08-" . str_pad($day, 2, '0', STR_PAD_LEFT);
        })->toArray();

        for ($i = 1; $i <= 7; $i++) {
            DB::table('pendaftaran_sidang')->insert([
                'mahasiswa_id' => $i,
                'tanggal_pendaftaran' => $tanggal[$i - 1],
                'file_tugas_akhir' => "file_tugas_akhir_mahasiswa{$i}.pdf",
                'file_bebas_pinjaman_administrasi' => "bebas_adm_mahasiswa{$i}.pdf",
                'file_slip_pembayaran_semester_akhir' => "slip_semester_akhir_mahasiswa{$i}.pdf",
                'file_transkip_sementara' => "transkip_mahasiswa{$i}.pdf",
                'file_bukti_pembayaran_sidang_ta' => "bukti_sidang_mahasiswa{$i}.pdf",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
