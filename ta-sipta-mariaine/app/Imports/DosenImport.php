<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Dosen;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class DosenImport implements ToCollection
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

                    // Validasi unik
                    $existingUser = User::where('name', $row[0])->orWhere('email', $row[1])->first();
                    $existingDosen = Dosen::where('nip', $row[2])->first();
                    if ($existingUser || $existingDosen) {
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
                        'role' => 'Dosen',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Buat dosen
                    Dosen::create([
                        'user_id' => $user->id,
                        'nama_dosen' => $row[0],
                        'nip' => $row[2],
                        'tempat_lahir' => $row[3],
                        'tanggal_lahir' => $tanggalLahir,
                        'jenis_kelamin' => $row[5],
                    ]);
                }
            });

            if (!empty($skippedRows)) {
                session()->flash('error', 'Beberapa baris gagal diimpor: Baris ' . implode(', ', $skippedRows));
            } else {
                session()->flash('success', 'Semua data dosen berhasil diimpor.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }
}
