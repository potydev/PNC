<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\Mahasiswa;
use App\Models\RuanganSidang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class JadwalSidangTugasAkhirImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        $skippedRows = [];
        $jadwalList = [];

        try {
            DB::transaction(function () use ($collection, &$skippedRows, &$jadwalList) {
                $now = now();
                foreach ($collection as $key => $row) {
                    if ($key === 0 || $row->filter()->isEmpty()) continue;

                    $baris = $key + 1;

                    try {
                        $namaMahasiswa = $row[0];
                        $jenisSidang = strtolower(trim($row[1]));

                        if (!in_array($jenisSidang, ['sidang reguler', 'sidang ulang'])) {
                            throw new \Exception("Jenis sidang tidak valid, isikan 'Sidang Reguler' atau 'Sidang Ulang'.");
                        }

                        $mahasiswaId = is_numeric($row[0])
                            ? Mahasiswa::find($row[0])?->id
                            : Mahasiswa::where('nama_mahasiswa', $row[0])->value('id');

                        if (!$mahasiswaId) {
                            throw new \Exception("Mahasiswa '{$row[0]}' tidak ditemukan.");
                        }

                        $exists = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)
                            ->where('jenis_sidang', $jenisSidang)
                            ->exists();

                        if ($exists) {
                            throw new \Exception("Mahasiswa '{$namaMahasiswa}' sudah memiliki jadwal untuk jenis sidang '{$jenisSidang}'.");
                        }

                        // Ambil ID dosen
                        $pembimbingUtamaId = is_numeric($row[2]) ? Dosen::find($row[2])?->id : Dosen::where('nama_dosen', $row[2])->value('id');
                        $pembimbingPendampingId = is_numeric($row[3]) ? Dosen::find($row[3])?->id : Dosen::where('nama_dosen', $row[3])->value('id');
                        $pengujiUtamaId = is_numeric($row[4]) ? Dosen::find($row[4])?->id : Dosen::where('nama_dosen', $row[4])->value('id');
                        $pengujiPendampingId = is_numeric($row[5]) ? Dosen::find($row[5])?->id : Dosen::where('nama_dosen', $row[5])->value('id');

                        if (count([$pembimbingUtamaId, $pembimbingPendampingId, $pengujiUtamaId, $pengujiPendampingId]) !== count(array_unique([$pembimbingUtamaId, $pembimbingPendampingId, $pengujiUtamaId, $pengujiPendampingId]))) {
                            throw new \Exception("Dosen tidak boleh sama di kategori berbeda.");
                        }

                        // Konversi tanggal
                        try {
                            $tanggal = is_numeric($row[6])
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[6])->format('Y-m-d')
                                : \Carbon\Carbon::parse($row[6])->format('Y-m-d');
                        } catch (\Exception $e) {
                            throw new \Exception("Format tanggal tidak valid: {$row[6]}");
                        }

                        // Konversi waktu
                        try {
                            $waktuMulai = is_numeric($row[7])
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[7])->format('H:i:s')
                                : \Carbon\Carbon::parse($row[7])->format('H:i:s');

                            $waktuSelesai = is_numeric($row[8])
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8])->format('H:i:s')
                                : \Carbon\Carbon::parse($row[8])->format('H:i:s');
                        } catch (\Exception $e) {
                            throw new \Exception("Format waktu tidak valid: {$row[7]} - {$row[8]}");
                        }

                        // Ruangan
                        $ruanganSidangId = is_numeric($row[9])
                            ? RuanganSidang::find($row[9])?->id
                            : RuanganSidang::where('nama_ruangan', $row[9])->value('id');

                        if (!$ruanganSidangId) {
                            throw new \Exception("Ruangan '{$row[9]}' tidak ditemukan.");
                        }

                        // Cek bentrok antar baris file
                        foreach ($jadwalList as $prev) {
                            if (
                                $prev['tanggal'] === $tanggal &&
                                $prev['waktu_mulai'] === $waktuMulai &&
                                $prev['jenis_sidang'] === $jenisSidang
                            ) {
                                if ($prev['ruangan_sidang_id'] === $ruanganSidangId) {
                                    throw new \Exception("Bentrok ruangan dengan '{$prev['nama_mahasiswa']}' pada {$tanggal} {$waktuMulai}");
                                }

                                $dosen1 = [$pembimbingUtamaId, $pembimbingPendampingId, $pengujiUtamaId, $pengujiPendampingId];
                                $dosen2 = [$prev['pembimbing_utama_id'], $prev['pembimbing_pendamping_id'], $prev['penguji_utama_id'], $prev['penguji_pendamping_id']];
                                if (array_intersect($dosen1, $dosen2)) {
                                    throw new \Exception("Bentrok dosen dengan '{$prev['nama_mahasiswa']}' pada {$tanggal} {$waktuMulai}");
                                }
                            }
                        }

                        $jadwalList[] = [
                            'baris_ke' => $baris,
                            'mahasiswa_id' => $mahasiswaId,
                            'nama_mahasiswa' => $namaMahasiswa,
                            'pembimbing_utama_id' => $pembimbingUtamaId,
                            'pembimbing_pendamping_id' => $pembimbingPendampingId,
                            'penguji_utama_id' => $pengujiUtamaId,
                            'penguji_pendamping_id' => $pengujiPendampingId,
                            'tanggal' => $tanggal,
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => $waktuSelesai,
                            'ruangan_sidang_id' => $ruanganSidangId,
                            'jenis_sidang' => $jenisSidang,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    } catch (\Exception $e) {
                        $skippedRows[] = "Baris {$baris}: " . $e->getMessage();
                        continue;
                    }
                }

                foreach ($jadwalList as $jadwal) {
                    unset($jadwal['baris_ke'], $jadwal['nama_mahasiswa']);
                    JadwalSidangTugasAkhir::create($jadwal);
                }
            });

            if (!empty($skippedRows)) {
                session()->flash('error', 'Beberapa baris gagal diimpor:<br>' . implode('<br>', $skippedRows));
            } else {
                session()->flash('success', 'Semua data jadwal sidang tugas akhir berhasil diimpor.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}
