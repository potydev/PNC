<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;

class MahasiswaImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        $skippedRows = [];

        try {
            DB::transaction(function () use ($collection, &$skippedRows) {
                foreach ($collection as $key => $row) {
                    if (collect($row)->filter()->isEmpty()) continue;

                    // Skip header
                    if ($key === 0) continue;

                    // Cek unik: name, email, nim
                    $existingUser = User::where('name', $row[0])->orWhere('email', $row[1])->first();
                    $existingMahasiswa = Mahasiswa::where('nim', $row[2])->first();
                    if ($existingUser || $existingMahasiswa) {
                        $skippedRows[] = $key + 1; // tambahkan +1 untuk menyamakan baris Excel
                        continue;
                    }

                    // Cek Program Studi
                    $programStudiId = null;
                    if (is_numeric($row[6])) {
                        $programStudiId = ProgramStudi::find($row[6])?->id;
                    } else {
                        $programStudi = ProgramStudi::where('nama_prodi', $row[6])->first();
                        $programStudiId = $programStudi?->id;
                    }

                    // Cek Tahun Ajaran
                    $tahunAjaranId = null;
                    if (is_numeric($row[7])) {
                        $tahunAjaranId = TahunAjaran::find($row[7])?->id;
                    } else {
                        $tahunAjaran = TahunAjaran::where('tahun_ajaran', $row[7])->first();
                        $tahunAjaranId = $tahunAjaran?->id;
                    }

                    if (!$programStudiId || !$tahunAjaranId) {
                        $skippedRows[] = $key + 1;
                        continue;
                    }

                    // Format tanggal lahir
                    try {
                        $tanggalLahir = \Carbon\Carbon::parse($row[4])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $skippedRows[] = $key + 1;
                        continue;
                    }

                    // Buat user
                    $user = User::create([
                        'name' => $row[0],
                        'email' => $row[1],
                        'email_verified_at' => now(),
                        'password' => Hash::make('11111111'),
                        'role' => 'Mahasiswa',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Buat mahasiswa
                    Mahasiswa::create([
                        'user_id' => $user->id,
                        'nama_mahasiswa' => $row[0],
                        'nim' => $row[2],
                        'tempat_lahir' => $row[3],
                        'tanggal_lahir' => $tanggalLahir,
                        'jenis_kelamin' => $row[5],
                        'program_studi_id' => $programStudiId,
                        'tahun_ajaran_id' => $tahunAjaranId,
                    ]);
                }
            });

            if (!empty($skippedRows)) {
                session()->flash('error', 'Beberapa baris gagal diimpor: Baris ' . implode(', ', $skippedRows));
            } else {
                session()->flash('success', 'Semua data mahasiswa berhasil diimpor.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data mahasiswa: ' . $e->getMessage());
        }
    }
}
