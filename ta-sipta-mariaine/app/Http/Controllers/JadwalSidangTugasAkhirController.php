<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\TahunAjaran;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use App\Models\RuanganSidang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\JadwalSidangTugasAkhir;
use App\Imports\JadwalSidangTugasAkhirImport;

\Carbon\Carbon::setLocale('id');

class JadwalSidangTugasAkhirController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        // Ambil semua jadwal sidang tugas akhir lengkap dengan relasi
        $jadwals = JadwalSidangTugasAkhir::with([
            'mahasiswa.programStudi',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])
            ->orderBy('created_at', 'desc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // Group berdasarkan nama program studi mahasiswa
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama_prodi ?? 'Tidak Diketahui';
        });

        // Ambil data dosen dan ruangan untuk kebutuhan dropdown / filter di view
        $programStudi = ProgramStudi::all();
        $pembimbingUtama = Dosen::all();
        $pembimbingPendamping = Dosen::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();
        $tahunAjaranList = TahunAjaran::all();


        // Kirim data ke view
        return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact(
            'jadwals',
            'jadwalsGrouped',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'programStudi',
            'tahunAjaranList',
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new JadwalSidangTugasAkhirImport, $request->file('file'));
            return redirect()->route('jadwal_sidang_tugas_akhir.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id|different:pembimbing_pendamping_id|different:penguji_utama_id|different:penguji_pendamping_id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id|different:penguji_utama_id|different:penguji_pendamping_id',
            'penguji_utama_id' => 'required|exists:dosen,id|different:penguji_pendamping_id',
            'penguji_pendamping_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i:s',
            'waktu_selesai' => 'required|date_format:H:i:s|after:waktu_mulai',
            'ruangan_sidang_id' => 'required|exists:ruangan_sidang,id',
        ]);

        $jadwal = JadwalSidangTugasAkhir::findOrFail($id);

        // Ambil data mahasiswa yang terkait
        $mahasiswa = $jadwal->mahasiswa;

        // ===== POIN 1: Cek dosen tidak boleh sama =====
        $dosenDipakai = [
            $request->pembimbing_utama_id,
            $request->pembimbing_pendamping_id,
            $request->penguji_utama_id,
            $request->penguji_pendamping_id,
        ];

        if (count(array_unique($dosenDipakai)) < count($dosenDipakai)) {
            return back()->withErrors(['error' => 'Pembimbing dan penguji tidak boleh sama.'])->withInput();
        }

        // ===== POIN 2: Cek jadwal tabrakan (tanggal, waktu, ruangan) =====
        $jadwalBentrok = JadwalSidangTugasAkhir::where('id', '!=', $id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_mulai', $request->waktu_mulai)
            ->where('waktu_selesai', $request->waktu_selesai)
            ->where('ruangan_sidang_id', $request->ruangan_sidang_id)
            ->exists();

        if ($jadwalBentrok) {
            return back()->withErrors(['error' => 'Jadwal dengan waktu dan ruangan yang sama sudah terpakai.'])->withInput();
        }

        // ===== POIN 3: Cek dosen bentrok pada waktu yang sama tapi beda ruangan =====
        $jadwalKonflikDosen = JadwalSidangTugasAkhir::where('id', '!=', $id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_mulai', $request->waktu_mulai)
            ->where('waktu_selesai', $request->waktu_selesai)
            ->where(function ($query) use ($request) {
                $query->whereIn('pembimbing_utama_id', [
                    $request->pembimbing_utama_id,
                    $request->pembimbing_pendamping_id,
                    $request->penguji_utama_id,
                    $request->penguji_pendamping_id
                ])
                    ->orWhereIn('pembimbing_pendamping_id', [
                        $request->pembimbing_utama_id,
                        $request->pembimbing_pendamping_id,
                        $request->penguji_utama_id,
                        $request->penguji_pendamping_id
                    ])
                    ->orWhereIn('penguji_utama_id', [
                        $request->pembimbing_utama_id,
                        $request->pembimbing_pendamping_id,
                        $request->penguji_utama_id,
                        $request->penguji_pendamping_id
                    ])
                    ->orWhereIn('penguji_pendamping_id', [
                        $request->pembimbing_utama_id,
                        $request->pembimbing_pendamping_id,
                        $request->penguji_utama_id,
                        $request->penguji_pendamping_id
                    ]);
            })
            ->exists();

        if ($jadwalKonflikDosen) {
            return back()->withErrors(['error' => 'Salah satu dosen telah memiliki jadwal lain di waktu yang sama meskipun ruangan berbeda.'])->withInput();
        }

        // Update jika semua valid
        $jadwal->update($request->all());

        return redirect()->route('jadwal_sidang_tugas_akhir.index')->with('success', 'Data jadwal sidang tugas akhir berhasil diperbarui');
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        $programStudi = ProgramStudi::all(); // Daftar prodi
        $pembimbingUtama = Dosen::all();
        $pembimbingPendamping = Dosen::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();

        // 🔍 Ambil input filter
        $programStudiId = $request->input('program_studi');
        $tahunAjaran = $request->input('tahun_ajaran');
        $dosenId = $request->input('dosen_id');

        // 🔎 Ambil dan filter jadwal
        $jadwals = JadwalSidangTugasAkhir::with([
            'mahasiswa.programStudi',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])
            ->when($programStudiId, function ($query) use ($programStudiId) {
                $query->whereHas('mahasiswa', function ($q) use ($programStudiId) {
                    $q->where('program_studi_id', $programStudiId);
                });
            })
            ->when($tahunAjaran, function ($query) use ($tahunAjaran) {
                $query->whereHas('mahasiswa', function ($q) use ($tahunAjaran) {
                    $q->where('tahun_ajaran_id', $tahunAjaran);
                });
            })
            ->when($dosenId, function ($query) use ($dosenId) {
                $query->where(function ($q) use ($dosenId) {
                    $q->where('pembimbing_utama_id', $dosenId)
                        ->orWhere('pembimbing_pendamping_id', $dosenId)
                        ->orWhere('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')

            ->get();

        // 🔁 Grouping berdasarkan nama prodi
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama_prodi ?? 'Tidak Diketahui';
        });

        // Ambil daftar tahun ajaran dari model TahunAjaran
        $tahunAjaranList = TahunAjaran::all();

        return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact(
            'jadwals',
            'jadwalsGrouped',
            'programStudi',
            'user',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'tahunAjaranList',
            'dosenId',
            'tahunAjaran'
        ));
    }

    public function rekapDosenPenguji(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->dosen->jabatan !== 'Koordinator Program Studi') {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $programStudiId = $user->dosen->program_studi_id;
        $tahunAjaranId = $request->input('tahun_ajaran');

        $data = JadwalSidangTugasAkhir::with(['mahasiswa'])
            ->whereHas('mahasiswa', function ($q) use ($programStudiId, $tahunAjaranId) {
                $q->where('program_studi_id', $programStudiId);
                if ($tahunAjaranId) {
                    $q->where('tahun_ajaran_id', $tahunAjaranId);
                }
            })
            ->get();

        $grouped = [];

        foreach ($data as $item) {
            $mhs = $item->mahasiswa;

            // Penguji Utama
            if ($item->penguji_utama_id && $item->pengujiUtama) {
                $nama = $item->pengujiUtama->nama_dosen;

                if (!isset($grouped[$nama])) {
                    $grouped[$nama] = [
                        'nama_dosen' => $nama,
                        'detail' => [],
                    ];
                }

                $grouped[$nama]['detail'][] = (object)[
                    'peran' => 'Penguji Utama',
                    'tahun_ajaran' => $mhs->tahunAjaran->tahun_ajaran,
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                ];
            }

            // Penguji Pendamping
            if ($item->penguji_pendamping_id && $item->pengujiPendamping) {
                $nama = $item->pengujiPendamping->nama_dosen;

                if (!isset($grouped[$nama])) {
                    $grouped[$nama] = [
                        'nama_dosen' => $nama,
                        'detail' => [],
                    ];
                }

                $grouped[$nama]['detail'][] = (object)[
                    'peran' => 'Penguji Pendamping',
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                    'tahun_ajaran' => $mhs->tahunAjaran->tahun_ajaran ?? '',
                ];
            }
        }

        $rekap = collect($grouped)
            ->flatMap(function ($item) {
                return collect($item['detail'])->map(function ($detail) use ($item) {
                    return [
                        'nama_dosen' => $item['nama_dosen'],
                        'peran' => $detail->peran,
                        'nim' => $detail->nim,
                        'nama_mahasiswa' => $detail->nama_mahasiswa,
                        'tahun_ajaran' => $detail->tahun_ajaran,
                    ];
                });
            })
            ->groupBy('tahun_ajaran')
            ->map(function ($dataPerTahun) {
                return collect($dataPerTahun)
                    ->groupBy('nama_dosen')
                    ->map(function ($items, $dosen) {
                        return [
                            'nama_dosen' => $dosen,
                            'detail' => collect($items)
                                ->sortBy([
                                    fn($a, $b) => strcmp($b['peran'], $a['peran']),
                                    fn($a, $b) => strcmp($a['nama_mahasiswa'], $b['nama_mahasiswa']),
                                ])
                                ->map(function ($x) {
                                    return (object) $x;
                                })
                                ->values()
                                ->all()
                        ];
                    })
                    ->sortKeys()
                    ->values()
                    ->all();
            })
            ->sortKeysDesc();


        $pdf = Pdf::loadView('jadwal_sidang.rekap_dosen_penguji', compact('rekap'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('rekap_dosen_penguji.pdf');
    }
}
