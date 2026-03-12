<?php

namespace Database\Seeders;

use App\Models\CatatanRevisiTA;
use App\Models\HasilSidang;
use App\Models\RuanganSidang;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            ProgramStudiSeeder::class,
            RuanganSidangSeeder::class,
            TahunAjaranSeeder::class,
            MahasiswaSeeder::class,
            DosenSeeder::class,
            ProposalSeeder::class,
            PengajuanPembimbingSeeder::class,
            JadwalSeminarProposalSeeder::class,
            PenilaianSemproSeeder::class,
            HasilAkhirSemproSeeder::class,
            JadwalBimbinganSeeder::class,
            PendaftaranBimbinganSeeder::class,
            LogbookBimbinganSeeder::class,
            PendaftaranSidangSeeder::class,
            JadwalSidangTugasAkhirSeeder::class,
            RubrikNilaiSeeder::class,
            PenilaianTASeeder::class,
            HasilAkhirTASeeder::class,
            CatatanRevisiTASeeder::class,
            HasilSidangSeeder::class,
        ]);
    }
}
