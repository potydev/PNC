<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\RuanganSidang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalSeminarProposal;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;

class JadwalSeminarProposalImport implements ToCollection
{

    public function collection(Collection $collection)
    {
        $skippedRows = [];
        $jadwalList = [];

        try {
            DB::transaction(function () use ($collection, &$skippedRows, &$jadwalList) {
                foreach ($collection as $key => $row) {
                    $now = now();

                    if ($key === 0 || collect($row)->filter()->isEmpty()) continue;

                    try {
                        if (count($row) < 7) {
                            $skippedRows[] = "Baris " . ($key + 1) . ": Data tidak lengkap.";
                            continue;
                        }

                        $baris = $key + 1;

                        $mahasiswaId = is_numeric($row[0])
                            ? Mahasiswa::find($row[0])?->id
                            : Mahasiswa::where('nama_mahasiswa', $row[0])->value('id');

                        if (!$mahasiswaId) {
                            $skippedRows[] = "Baris {$baris}: Mahasiswa '{$row[0]}' tidak ditemukan.";
                            continue;
                        }

                        if (JadwalSeminarProposal::where('mahasiswa_id', $mahasiswaId)->exists()) {
                            $skippedRows[] = "Baris {$baris}: Mahasiswa '{$row[0]}' sudah memiliki jadwal seminar.";
                            continue;
                        }

                        $pengujiUtamaId = is_numeric($row[1])
                            ? Dosen::find($row[1])?->id
                            : Dosen::where('nama_dosen', $row[1])->value('id');

                        if (!$pengujiUtamaId) {
                            $skippedRows[] = "Baris {$baris}: Dosen penguji utama '{$row[1]}' tidak ditemukan.";
                            continue;
                        }

                        $pengujiPendampingId = is_numeric($row[2])
                            ? Dosen::find($row[2])?->id
                            : Dosen::where('nama_dosen', $row[2])->value('id');

                        if (!$pengujiPendampingId) {
                            $skippedRows[] = "Baris {$baris}: Dosen penguji pendamping '{$row[2]}' tidak ditemukan.";
                            continue;
                        }

                        if ($pengujiUtamaId === $pengujiPendampingId) {
                            $skippedRows[] = "Baris {$baris}: Dosen penguji utama dan pendamping tidak boleh sama.";
                            continue;
                        }

                        try {
                            $tanggal = is_numeric($row[3])
                                ? Date::excelToDateTimeObject($row[3])->format('Y-m-d')
                                : \Carbon\Carbon::parse($row[3])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $skippedRows[] = "Baris {$baris}: Format tanggal tidak valid ({$row[3]}).";
                            continue;
                        }

                        try {
                            $waktuMulai = is_numeric($row[4])
                                ? Date::excelToDateTimeObject($row[4])->format('H:i:s')
                                : \Carbon\Carbon::parse($row[4])->format('H:i:s');

                            $waktuSelesai = is_numeric($row[5])
                                ? Date::excelToDateTimeObject($row[5])->format('H:i:s')
                                : \Carbon\Carbon::parse($row[5])->format('H:i:s');
                        } catch (\Exception $e) {
                            $skippedRows[] = "Baris {$baris}: Format waktu tidak valid ({$row[4]} - {$row[5]}).";
                            continue;
                        }

                        $ruanganSidangId = is_numeric($row[6])
                            ? RuanganSidang::find($row[6])?->id
                            : RuanganSidang::where('nama_ruangan', $row[6])->value('id');

                        if (!$ruanganSidangId) {
                            $skippedRows[] = "Baris {$baris}: Ruangan '{$row[6]}' tidak ditemukan.";
                            continue;
                        }

                        // 🔍 Cek bentrok di database
                        $bentrokMessages = [];

                        $bentrokRuangan = JadwalSeminarProposal::where('tanggal', $tanggal)
                            ->where('waktu_mulai', $waktuMulai)
                            ->where('ruangan_sidang_id', $ruanganSidangId)
                            ->first();

                        if ($bentrokRuangan) {
                            $nama = optional($bentrokRuangan->mahasiswa)->nama_mahasiswa ?? 'tidak diketahui';
                            $bentrokMessages[] = "ruangan bentrok dengan '{$nama}' pada {$tanggal} {$waktuMulai}";
                        }

                        $bentrokPengujiUtama = JadwalSeminarProposal::where('tanggal', $tanggal)
                            ->where('waktu_mulai', $waktuMulai)
                            ->where('penguji_utama_id', $pengujiUtamaId)
                            ->first();

                        if ($bentrokPengujiUtama) {
                            $nama = optional($bentrokPengujiUtama->mahasiswa)->nama_mahasiswa ?? 'tidak diketahui';
                            $bentrokMessages[] = "penguji utama bentrok dengan '{$nama}' pada {$tanggal} {$waktuMulai}";
                        }

                        $bentrokPengujiPendamping = JadwalSeminarProposal::where('tanggal', $tanggal)
                            ->where('waktu_mulai', $waktuMulai)
                            ->where('penguji_pendamping_id', $pengujiPendampingId)
                            ->first();

                        if ($bentrokPengujiPendamping) {
                            $nama = optional($bentrokPengujiPendamping->mahasiswa)->nama_mahasiswa ?? 'tidak diketahui';
                            $bentrokMessages[] = "penguji pendamping bentrok dengan '{$nama}' pada {$tanggal} {$waktuMulai}";
                        }

                        if (!empty($bentrokMessages)) {
                            $skippedRows[] = "Baris {$baris}: " . implode(', ', $bentrokMessages);
                            continue;
                        }

                        // 🔍 Cek bentrok antar baris dalam file
                        foreach ($jadwalList as $item) {
                            if ($item['tanggal'] === $tanggal && $item['waktu_mulai'] === $waktuMulai) {
                                $bentrokDalamFile = [];

                                if ($item['ruangan_sidang_id'] === $ruanganSidangId) {
                                    $bentrokDalamFile[] = "ruangan bentrok";
                                }
                                if ($item['penguji_utama_id'] === $pengujiUtamaId) {
                                    $bentrokDalamFile[] = "penguji utama bentrok";
                                }
                                if ($item['penguji_pendamping_id'] === $pengujiPendampingId) {
                                    $bentrokDalamFile[] = "penguji pendamping bentrok";
                                }

                                if (!empty($bentrokDalamFile)) {
                                    $skippedRows[] = "Baris {$baris}: Bentrok dengan baris {$item['baris_ke']} dalam file (" . implode(', ', $bentrokDalamFile) . " pada {$tanggal} {$waktuMulai})";
                                    continue 2;
                                }
                            }
                        }

                        // Simpan ke DB
                        JadwalSeminarProposal::create([
                            'mahasiswa_id' => $mahasiswaId,
                            'penguji_utama_id' => $pengujiUtamaId,
                            'penguji_pendamping_id' => $pengujiPendampingId,
                            'tanggal' => $tanggal,
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => $waktuSelesai,
                            'ruangan_sidang_id' => $ruanganSidangId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                        $jadwalList[] = [
                            'baris_ke' => $baris,
                            'tanggal' => $tanggal,
                            'waktu_mulai' => $waktuMulai,
                            'ruangan_sidang_id' => $ruanganSidangId,
                            'penguji_utama_id' => $pengujiUtamaId,
                            'penguji_pendamping_id' => $pengujiPendampingId,
                        ];
                    } catch (\Exception $e) {
                        $skippedRows[] = "Baris {$baris}: Kesalahan sistem - " . $e->getMessage();
                    }
                }
            });

            if (!empty($skippedRows)) {
                session()->flash('error', 'Beberapa baris gagal diimpor:<br>' . implode('<br>', $skippedRows));
            } else {
                session()->flash('success', 'Semua data jadwal seminar proposal berhasil diimpor.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}
