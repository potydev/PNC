<?php


namespace App\Http\Controllers;

use setasign\Fpdi\Fpdi;
use App\Models\HasilSidang;

use App\Models\TahunAjaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RiwayatSidang;
use App\Models\CatatanRevisiTA;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\{Dosen, HasilAkhirTA, PenilaianTA, Mahasiswa, RubrikNilai, JadwalSeminarProposal, JadwalSidangTugasAkhir};

\Carbon\Carbon::setLocale('id');


class PenilaianTAController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'jadwal_sidang_tugas_akhir_id' => ['required', 'exists:jadwal_sidang_tugas_akhir,id'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $mahasiswaId = $request->mahasiswa_id;
        $dosenId = $request->dosen_id;
        $jadwalId = $request->jadwal_sidang_tugas_akhir_id;

        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        $programStudiId = $mahasiswa->program_studi_id;

        $jenisDosen = null;

        foreach ($request->nilai as $rubrikId => $nilai) {
            $rubrik = RubrikNilai::where('id', $rubrikId)
                ->where('program_studi_id', $programStudiId)
                ->first();

            if (!$rubrik) continue;

            if (!$jenisDosen) {
                $jenisDosen = $rubrik->jenis_dosen;
            }

            PenilaianTA::updateOrCreate(
                [
                    'mahasiswa_id' => $mahasiswaId,
                    'dosen_id' => $dosenId,
                    'rubrik_id' => $rubrikId,
                    'jadwal_sidang_tugas_akhir_id' => $jadwalId,
                ],
                ['nilai' => $nilai]
            );
        }

        if (!$jenisDosen) {
            return back()->with('error', 'Rubrik tidak ditemukan atau tidak valid.');
        }

        $penilaian = PenilaianTA::where('mahasiswa_id', $mahasiswaId)
            ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
            ->with('rubrik')
            ->get()
            ->groupBy(fn($item) => $item->rubrik->jenis_dosen);

        $nilai = [
            'Pembimbing Utama' => optional($penilaian->get('Pembimbing Utama'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
            'Pembimbing Pendamping' => optional($penilaian->get('Pembimbing Pendamping'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
            'Penguji Utama' => optional($penilaian->get('Penguji Utama'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
            'Penguji Pendamping' => optional($penilaian->get('Penguji Pendamping'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
        ];

        $semuaAda = collect($nilai)->every(fn($n) => $n !== null);

        $kaprodi = Dosen::where('jabatan', 'Koordinator Program Studi')
            ->where('program_studi_id', $programStudiId)
            ->first();

        $hasilAkhir = HasilAkhirTA::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswaId,
                'jadwal_sidang_tugas_akhir_id' => $jadwalId,
            ],
            [
                'kaprodi_id' => $kaprodi?->id,
                'nilai_pembimbing_utama' => $nilai['Pembimbing Utama'],
                'nilai_pembimbing_pendamping' => $nilai['Pembimbing Pendamping'],
                'nilai_penguji_utama' => $nilai['Penguji Utama'],
                'nilai_penguji_pendamping' => $nilai['Penguji Pendamping'],
                'total_akhir' => $semuaAda
                    ? ($nilai['Pembimbing Utama'] * 0.3) +
                    ($nilai['Pembimbing Pendamping'] * 0.3) +
                    ($nilai['Penguji Utama'] * 0.2) +
                    ($nilai['Penguji Pendamping'] * 0.2)
                    : null,
            ]
        );

        if ($hasilAkhir->total_akhir !== null) {
            $rataRataNilai = ($hasilAkhir->nilai_penguji_utama + $hasilAkhir->nilai_penguji_pendamping) / 2;

            // Periksa apakah ada catatan revisi
            $punyaCatatanRevisi = CatatanRevisiTA::where('mahasiswa_id', $mahasiswaId)
                ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
                ->exists();

            if (!$punyaCatatanRevisi) {
                $statusSidang = 'Lulus';
            } else {
                $statusSidang = $rataRataNilai < 50 ? 'Sidang Ulang' : 'Revisi';
            }

            $hasilSidang = HasilSidang::updateOrCreate(
                ['mahasiswa_id' => $mahasiswaId],
                [
                    'status_kelulusan' => $statusSidang,
                    'tahun_lulus' => in_array($statusSidang, ['Lulus', 'Revisi']) ? now()->format('Y') : null,
                ]
            );

            $sudahAdaRiwayat = RiwayatSidang::where('hasil_sidang_id', $hasilSidang->id)
                ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
                ->exists();

            if (!$sudahAdaRiwayat) {
                RiwayatSidang::create([
                    'hasil_sidang_id' => $hasilSidang->id,
                    'jadwal_sidang_tugas_akhir_id' => $jadwalId,
                    'hasil_akhir_ta_id' => $hasilAkhir->id,
                    'status_sidang' => $statusSidang,
                ]);
            }

            $riwayatTerakhir = RiwayatSidang::where('hasil_sidang_id', $hasilSidang->id)
                ->latest()
                ->first();

            if ($riwayatTerakhir) {
                $hasilSidang->update(['status_kelulusan' => $riwayatTerakhir->status_sidang]);
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function indexTugasAkhirDosen()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        $mahasiswa = Mahasiswa::with('jadwalSidangTugasAkhir')->get();

        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.penilaianTA',
            'mahasiswa.pendaftaranSidang'
        ])
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->orderBy('tanggal', 'desc')->get();

        foreach ($sidang as $item) {
            // Tentukan peran dosen
            $peran = null;
            if ($item->pembimbing_utama_id === $dosenId) $peran = 'pembimbing_utama';
            elseif ($item->pembimbing_pendamping_id === $dosenId) $peran = 'pembimbing_pendamping';
            elseif ($item->penguji_utama_id === $dosenId) $peran = 'penguji_utama';
            elseif ($item->penguji_pendamping_id === $dosenId) $peran = 'penguji_pendamping';

            $programStudiId = $item->mahasiswa->program_studi_id;

            $rubrik = RubrikNilai::where('jenis_dosen', $peran)
                ->where('program_studi_id', $programStudiId)
                ->orderBy('id')
                ->get();


            // Ambil nilai existing dari penilaian_ta
            $existing = PenilaianTA::where('mahasiswa_id', $item->mahasiswa_id)
                ->where('dosen_id', $dosenId)
                ->get()
                ->keyBy('rubrik_id');

            // Tandai apakah kelompok perlu ditampilkan, isi nilai dan readonly
            $kelompokLalu = null;
            foreach ($rubrik as $r) {
                $r->nilai = $existing[$r->id]->nilai ?? null;
                $r->readonly = $r->nilai !== null;
                $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
                $kelompokLalu = $r->kelompok;
            }

            $item->peran = $peran;
            $item->rubrik = $rubrik;
            $item->nilai_eksisting = $existing;
            $item->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
            $item->dosen_id = $dosenId;
        }

        return view('penilaian.tugas_akhir', compact('sidang', 'dosen', 'dosenId', 'mahasiswa', 'user'));
    }

    public function form($sidangId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        // Cari data sidang berdasarkan ID + validasi dosen login
        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.penilaianTA',
            'mahasiswa.pendaftaranSidang',
            'ruanganSidang',
        ])
            ->where('id', $sidangId)
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->first();

        if (!$sidang) {
            return redirect()->back()->with('error', 'Data sidang tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Tentukan peran dosen dalam sidang ini
        $peran = match (true) {
            $sidang->pembimbing_utama_id === $dosenId => 'Pembimbing Utama',
            $sidang->pembimbing_pendamping_id === $dosenId => 'Pembimbing Pendamping',
            $sidang->penguji_utama_id === $dosenId => 'Penguji Utama',
            $sidang->penguji_pendamping_id === $dosenId => 'Penguji Pendamping',
            default => null,
        };

        $programStudiId = $sidang->mahasiswa->program_studi_id;

        $rubrik = RubrikNilai::where('jenis_dosen', $peran)
            ->where('program_studi_id', $programStudiId)
            ->orderBy('id')
            ->get();


        $totalPersentase = $rubrik->sum('persentase');
        $sidang->total_persentase = $totalPersentase;
        $sidang->rubrik_valid = $totalPersentase === 100;

        // Ambil nilai yang sudah diisi oleh dosen ini untuk sidang ini
        $existing = PenilaianTA::where('mahasiswa_id', $sidang->mahasiswa_id)
            ->where('dosen_id', $dosenId)
            ->where('jadwal_sidang_tugas_akhir_id', $sidang->id)
            ->get()
            ->keyBy('rubrik_id');

        $kelompokLalu = null;
        foreach ($rubrik as $r) {
            $r->nilai = $existing[$r->id]->nilai ?? null;
            $r->readonly = $r->nilai !== null;
            $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
            $kelompokLalu = $r->kelompok;
        }

        // Ambil nilai akhir mahasiswa
        $hasilAkhir = HasilAkhirTA::where('mahasiswa_id', $sidang->mahasiswa_id)->first();

        $sidang->total_nilai_akhir = match ($peran) {
            'Pembimbing Utama' => $hasilAkhir->nilai_pembimbing_utama ?? null,
            'Pembimbing Pendamping' => $hasilAkhir->nilai_pembimbing_pendamping ?? null,
            'Penguji Utama' => $hasilAkhir->nilai_penguji_utama ?? null,
            'Penguji Pendamping' => $hasilAkhir->nilai_penguji_pendamping ?? null,
            default => null,
        };

        $sidang->peran = $peran;
        $sidang->rubrik = $rubrik;
        $sidang->nilai_eksisting = $existing;
        $sidang->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
        $sidang->dosen_id = $dosenId;

        return view('penilaian.form', compact('sidang', 'dosen'));
    }

    public function cetakPDF($id)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.penilaianTA',
            'mahasiswa.pendaftaranSidang',
            'ruanganSidang'
        ])
            ->where('mahasiswa_id', $id)
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->first();

        if (!$sidang) {
            return redirect()->back()->with('error', 'Data sidang tidak ditemukan.');
        }

        // Tentukan peran dosen
        $peran = match (true) {
            $sidang->pembimbing_utama_id === $dosenId => 'Pembimbing Utama',
            $sidang->pembimbing_pendamping_id === $dosenId => 'Pembimbing Pendamping',
            $sidang->penguji_utama_id === $dosenId => 'Penguji Utama',
            $sidang->penguji_pendamping_id === $dosenId => 'Penguji Pendamping',
            default => null,
        };

        $programStudiId = $sidang->mahasiswa->program_studi_id;

        $rubrik = RubrikNilai::where('jenis_dosen', $peran)
            ->where('program_studi_id', $programStudiId)
            ->orderBy('id')
            ->get();


        // Ambil nilai existing
        $existing = PenilaianTA::where('mahasiswa_id', $sidang->mahasiswa_id)
            ->where('dosen_id', $dosenId)
            ->get()
            ->keyBy('rubrik_id');

        // Kelompok & nilai
        $kelompokLalu = null;
        foreach ($rubrik as $r) {
            $r->nilai = $existing[$r->id]->nilai ?? null;
            $r->readonly = $r->nilai !== null;
            $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
            $kelompokLalu = $r->kelompok;
        }

        $hasilAkhir = HasilAkhirTA::where('mahasiswa_id', $sidang->mahasiswa_id)->first();

        $sidang->total_nilai_akhir = match ($peran) {
            'Pembimbing Utama' => $hasilAkhir->nilai_pembimbing_utama ?? null,
            'Pembimbing Pendamping' => $hasilAkhir->nilai_pembimbing_pendamping ?? null,
            'Penguji Utama' => $hasilAkhir->nilai_penguji_utama ?? null,
            'Penguji Pendamping' => $hasilAkhir->nilai_penguji_pendamping ?? null,
            default => null,
        };

        $sidang->peran = $peran;
        $sidang->rubrik = $rubrik;
        $sidang->nilai_eksisting = $existing;
        $sidang->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
        $sidang->dosen_id = $dosenId;
        $dosenPenilai = Dosen::find($dosenId);

        $pdf = Pdf::loadView('penilaian.cetak', [
            'sidang' => $sidang,
            'dosen' => $dosenPenilai
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Form Penilaian ' . $sidang->mahasiswa->nama_mahasiswa . ' oleh ' . $dosen->nama_dosen . '.pdf');
    }

    public function gabungPenilaian($jadwalSidangId)
    {
        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa.penilaianTA',
            'mahasiswa.pendaftaranSidang',
            'ruanganSidang',
            'mahasiswa.programStudi',
            'mahasiswa.proposal'
        ])->findOrFail($jadwalSidangId);

        $mahasiswa = $sidang->mahasiswa;
        if (!$mahasiswa) {
            return back()->with('error', 'Mahasiswa tidak ditemukan pada jadwal sidang ini.');
        }

        $mahasiswaId = $mahasiswa->id;
        $namaMahasiswa = Str::slug($mahasiswa->nama_mahasiswa);

        $urutanPeran = [
            'Penguji Utama' => $sidang->penguji_utama_id,
            'Penguji Pendamping' => $sidang->penguji_pendamping_id,
            'Pembimbing Utama' => $sidang->pembimbing_utama_id,
            'Pembimbing Pendamping' => $sidang->pembimbing_pendamping_id,
        ];

        $paths = [];

        foreach ($urutanPeran as $peran => $dosenId) {
            if (!$dosenId) continue;

            $dosen = Dosen::find($dosenId);

            $rubrik = RubrikNilai::where([
                ['jenis_dosen', $peran],
                ['program_studi_id', $mahasiswa->program_studi_id]
            ])->orderBy('id')->get();

            $existing = PenilaianTA::where([
                ['mahasiswa_id', $mahasiswaId],
                ['dosen_id', $dosenId],
                ['jadwal_sidang_tugas_akhir_id', $jadwalSidangId],
            ])->get()->keyBy('rubrik_id');

            $kelompokLalu = null;
            foreach ($rubrik as $r) {
                $r->nilai = $existing[$r->id]->nilai ?? null;
                $r->readonly = true;
                $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
                $kelompokLalu = $r->kelompok;
            }

            $sidang->rubrik = $rubrik;
            $sidang->peran = $peran;
            $sidang->tanggal ??= now();

            $pdf = Pdf::loadView('penilaian.cetak', compact('sidang', 'dosen'))
                ->setPaper('A4', 'portrait');

            File::ensureDirectoryExists(storage_path('app/temp'));
            $filePath = storage_path("app/temp/penilaian_{$namaMahasiswa}_{$dosenId}.pdf");
            $pdf->save($filePath);

            $paths[] = $filePath;
        }

        // Gabungkan PDF
        $gabung = new Fpdi();
        foreach ($paths as $path) {
            $pageCount = $gabung->setSourceFile($path);
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $gabung->importPage($i);
                $size = $gabung->getTemplateSize($template);
                $gabung->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $gabung->useTemplate($template);
            }
        }

        foreach ($paths as $path) {
            File::delete($path);
        }

        $output = $gabung->Output('', 'S');
        $filename = "Penilaian_{$namaMahasiswa}.pdf";

        return response($output)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }

    public function lihatNilaiTA($jadwal)
    {
        $hasilAkhir = HasilAkhirTA::with(['mahasiswa', 'jadwalSidangTugasAkhir', 'kaprodi'])
            ->where('jadwal_sidang_tugas_akhir_id', $jadwal)
            ->firstOrFail();

        // Perhitungan bobot
        $nilai = [
            'Pembimbing Utama' => [
                'nilai' => $hasilAkhir->nilai_pembimbing_utama,
                'bobot' => 30,
            ],
            'Pembimbing Pendamping' => [
                'nilai' => $hasilAkhir->nilai_pembimbing_pendamping,
                'bobot' => 30,
            ],
            'Penguji Utama' => [
                'nilai' => $hasilAkhir->nilai_penguji_utama,
                'bobot' => 20,
            ],
            'Penguji Pendamping' => [
                'nilai' => $hasilAkhir->nilai_penguji_pendamping,
                'bobot' => 20,
            ],
        ];

        $totalJumlah = 0;

        foreach ($nilai as $key => $item) {
            $jumlah = $item['nilai'] ? ($item['nilai'] * $item['bobot'] / 100) : 0;
            $nilai[$key]['jumlah'] = $jumlah;
            $totalJumlah += $jumlah;
        }

        $statusKelulusan = optional($hasilAkhir->hasilSidang)->status_kelulusan ?? '-';

        $pdf = PDF::loadView('penilaian.form_penilaian_akhir', compact('hasilAkhir', 'nilai', 'totalJumlah', 'statusKelulusan'));
        return $pdf->stream('Form Penilaian Akhir ' . $hasilAkhir->mahasiswa->nama_mahasiswa . '.pdf');
    }

    public function indexRekapNilai(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        // Cek apakah user adalah Kaprodi atau bukan
        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $programStudiId = $user->dosen->program_studi_id;
        } else {
            $programStudiId = null; // Super Admin atau selain Kaprodi bisa lihat semua
        }

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $statusList = HasilSidang::select('status_kelulusan')->distinct()->pluck('status_kelulusan')->filter()->unique();

        $tahunAjaran = $request->input('tahun_ajaran');
        $status = $request->input('status_kelulusan');

        $hasilAkhirAll = HasilAkhirTA::select('hasil_akhir_ta.*')
            ->join('mahasiswa', 'mahasiswa.id', '=', 'hasil_akhir_ta.mahasiswa_id')
            ->join('tahun_ajaran', 'tahun_ajaran.id', '=', 'mahasiswa.tahun_ajaran_id')
            ->with(['mahasiswa.programStudi', 'mahasiswa.tahunAjaran'])
            ->when($programStudiId !== null, function ($query) use ($programStudiId) {
                $query->where('mahasiswa.program_studi_id', $programStudiId);
            })
            ->when($tahunAjaran, function ($query) use ($tahunAjaran) {
                $query->where('mahasiswa.tahun_ajaran_id', $tahunAjaran);
            })
            ->when($status, function ($query) use ($status) {
                $query->whereHas('mahasiswa.hasilSidang', function ($q) use ($status) {
                    $q->where('status_kelulusan', $status);
                });
            })
            ->whereNotNull('hasil_akhir_ta.total_akhir')
            ->orderBy('tahun_ajaran.tahun_ajaran', 'desc')
            ->orderBy('mahasiswa.nama_mahasiswa', 'asc')
            ->paginate(10)
            ->withQueryString();

        $rekap = $hasilAkhirAll->map(function ($item) {
            $nilai = $item->total_akhir;
            $huruf = match (true) {
                $nilai >= 80 => 'A',
                $nilai >= 75 => 'AB',
                $nilai >= 65 => 'B',
                $nilai >= 60 => 'BC',
                $nilai >= 50 => 'C',
                $nilai >= 40 => 'D',
                default => 'E',
            };

            return [
                'nim' => $item->mahasiswa->nim,
                'nama' => $item->mahasiswa->nama_mahasiswa,
                'prodi' => $item->mahasiswa->programStudi->nama_prodi ?? '-',
                'tahun_ajaran' => $item->mahasiswa->tahunAjaran->tahun_ajaran ?? '-',
                'total_angka' => $nilai,
                'total_huruf' => $huruf,
            ];
        });

        return view('penilaian.rekap_nilai', compact('tahunAjaranList', 'statusList', 'rekap', 'hasilAkhirAll', 'user'));
    }

    public function cetakRekapNilai(Request $request)
    {
        // dd('Masuk ke cetak!');
        $tahunAjaran = $request->input('tahun_ajaran');
        $status = $request->input('status_kelulusan');

        $user = Auth::user();
        $programStudiId = $user->dosen->program_studi_id;

        $hasilAkhirAll = HasilAkhirTA::with(['mahasiswa.programStudi', 'kaprodi'])
            ->whereHas('mahasiswa', function ($query) use ($programStudiId, $tahunAjaran) {
                $query->where('program_studi_id', $programStudiId);
                if ($tahunAjaran) {
                    $query->where('tahun_ajaran_id', $tahunAjaran);
                }
            })
            ->when($status, function ($query) use ($status) {
                $query->whereHas('mahasiswa.hasilSidang', function ($q) use ($status) {
                    $q->where('status_kelulusan', $status);
                });
            })->get()
            ->sortBy([
                // descending
                fn($a, $b) => strcmp($b->mahasiswa->tahunAjaran->tahun_ajaran ?? '', $a->mahasiswa->tahunAjaran->tahun_ajaran ?? ''),
                // ascending
                fn($a, $b) => strcmp($a->mahasiswa->nama_mahasiswa ?? '', $b->mahasiswa->nama_mahasiswa ?? ''),
            ])->values(); // reindex ulang biar rapih


        $kaprodi = $hasilAkhirAll->first()?->kaprodi;

        $rekap = $hasilAkhirAll->map(function ($item) {
            $nilai = $item->total_akhir;
            $huruf = match (true) {
                $nilai >= 80 => 'A',
                $nilai >= 75 => 'AB',
                $nilai >= 65 => 'B',
                $nilai >= 60 => 'BC',
                $nilai >= 50 => 'C',
                $nilai >= 40 => 'D',
                default => 'E',
            };

            return [
                'nim' => $item->mahasiswa->nim,
                'nama' => $item->mahasiswa->nama_mahasiswa,
                'prodi' => $item->mahasiswa->programStudi->nama_prodi ?? '-',
                'tahun_ajaran' => $item->mahasiswa->tahunAjaran->tahun_ajaran ?? '-',
                'total_angka' => $nilai,
                'total_huruf' => $huruf,
            ];
        });

        $pdf = PDF::loadView('penilaian.cetak_rekap_nilai', compact('rekap', 'kaprodi'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('rekap_nilai.pdf'); // Ganti dari download → stream dulu
    }

    public function cetakMahasiswaYudisium(Request $request)
    {
        $tahunAjaran = $request->input('tahun_ajaran');
        $status = $request->input('status_kelulusan');

        $user = Auth::user();
        $programStudiId = $user->dosen->program_studi_id;

        $hasilAkhirAll = HasilAkhirTA::with(['mahasiswa.programStudi', 'kaprodi'])
            ->whereHas('mahasiswa', function ($query) use ($programStudiId, $tahunAjaran) {
                $query->where('program_studi_id', $programStudiId);
                if ($tahunAjaran) {
                    $query->where('tahun_ajaran_id', $tahunAjaran);
                }
            })
            ->whereHas('mahasiswa.hasilSidang', function ($query) use ($status) {
                if ($status) {
                    $query->where('status_kelulusan', $status);
                }
                $query->where('kelengkapan_yudisium', 'Lengkap');
            })
            ->get();

        $kaprodi = $hasilAkhirAll->first()?->kaprodi;

        $rekap = $hasilAkhirAll->map(function ($item) {
            $nilai = $item->total_akhir;
            $huruf = match (true) {
                $nilai >= 80 => 'A',
                $nilai >= 75 => 'AB',
                $nilai >= 65 => 'B',
                $nilai >= 60 => 'BC',
                $nilai >= 50 => 'C',
                $nilai >= 40 => 'D',
                default => 'E',
            };

            return [
                'nim' => $item->mahasiswa->nim,
                'nama' => $item->mahasiswa->nama_mahasiswa,
                'prodi' => $item->mahasiswa->programStudi->nama_prodi ?? '-',
                'total_angka' => $nilai,
                'total_huruf' => $huruf,
            ];
        });

        $pdf = PDF::loadView('hasil_sidang.cetak_rekap_yudisium', compact('rekap', 'kaprodi'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('rekap_nilai_yudisium.pdf');
    }
}
