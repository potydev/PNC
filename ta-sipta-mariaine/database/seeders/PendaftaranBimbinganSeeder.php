<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranBimbinganSeeder extends Seeder
{
    public function run(): void
    {
        $pengajuan = DB::table('pengajuan_pembimbing')->where('validasi', 'Acc')->get();

        foreach ($pengajuan as $p) {
            $mahasiswaId = $p->mahasiswa_id;
            $pembimbingUtamaId = $p->pembimbing_utama_id;
            $pembimbingPendampingId = $p->pembimbing_pendamping_id;

            // Ambil semua jadwal pembimbing utama dan pendamping
            $jadwalPembimbing = DB::table('jadwal_bimbingan')
                ->whereIn('dosen_id', [$pembimbingUtamaId, $pembimbingPendampingId])
                ->orderBy('tanggal')
                ->get();

            foreach ($jadwalPembimbing as $jadwal) {
                DB::table('pendaftaran_bimbingan')->insert([
                    'mahasiswa_id' => $mahasiswaId,
                    'jadwal_bimbingan_id' => $jadwal->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
