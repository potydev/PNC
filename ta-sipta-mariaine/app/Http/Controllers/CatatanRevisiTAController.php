<?php

namespace App\Http\Controllers;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CatatanRevisiTA;
use App\Models\Dosen;
use App\Models\HasilAkhirTA;
use App\Models\HasilSidang;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\RiwayatSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanRevisiTAController extends Controller
{
    public function form(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized.');
        }

        $dosenId = $user->dosen->id;

        $jadwalId = $request->query('jadwal_sidang_tugas_akhir_id');
        $mahasiswaId = $request->query('mahasiswa_id');

        $jadwalSidang = JadwalSidangTugasAkhir::with([
            'ruanganSidang',
            'mahasiswa',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping'
        ])->findOrFail($jadwalId);

        // Cek peran dosen
        if ($jadwalSidang->pembimbing_utama_id == $dosenId) {
            $peran = 'pembimbing_utama';
            $dosen = $jadwalSidang->pembimbingUtama;
        } elseif ($jadwalSidang->pembimbing_pendamping_id == $dosenId) {
            $peran = 'pembimbing_pendamping';
            $dosen = $jadwalSidang->pembimbingPendamping;
        } elseif ($jadwalSidang->penguji_utama_id == $dosenId) {
            $peran = 'penguji_utama';
            $dosen = $jadwalSidang->pengujiUtama;
        } elseif ($jadwalSidang->penguji_pendamping_id == $dosenId) {
            $peran = 'penguji_pendamping';
            $dosen = $jadwalSidang->pengujiPendamping;
        } else {
            return abort(403, 'Anda bukan dosen yang terlibat di sidang ini.');
        }

        $catatan = CatatanRevisiTA::firstOrNew([
            'mahasiswa_id' => $mahasiswaId,
            'dosen_id' => $dosenId,
            'jadwal_sidang_tugas_akhir_id' => $jadwalId,
        ]);

        return view('catatan_revisi.form_tugas_akhir', compact('catatan', 'jadwalSidang', 'dosen', 'peran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'jadwal_sidang_tugas_akhir_id' => 'required|exists:jadwal_sidang_tugas_akhir,id',
            'catatan_revisi' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized.');
        }

        $dosenId = $user->dosen->id;

        $catatan = CatatanRevisiTA::updateOrCreate(
            [
                'mahasiswa_id' => $request->mahasiswa_id,
                'dosen_id' => $dosenId,
                'jadwal_sidang_tugas_akhir_id' => $request->jadwal_sidang_tugas_akhir_id,
            ],
            [
                'catatan_revisi' => $request->catatan_revisi,
            ]
        );

        // Update status sidang setelah revisi ditambahkan
        $mahasiswaId = $request->mahasiswa_id;
        $jadwalId = $request->jadwal_sidang_tugas_akhir_id;

        $hasilAkhir = HasilAkhirTA::where('mahasiswa_id', $mahasiswaId)
            ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
            ->first();

        if ($hasilAkhir && $hasilAkhir->total_akhir !== null) {
            $rataRataNilai = ($hasilAkhir->nilai_penguji_utama + $hasilAkhir->nilai_penguji_pendamping) / 2;
            $statusSidang = $rataRataNilai < 50 ? 'Sidang Ulang' : 'Revisi';

            $hasilSidang = HasilSidang::updateOrCreate(
                ['mahasiswa_id' => $mahasiswaId],
                [
                    'status_kelulusan' => $statusSidang,
                    'tahun_lulus' => in_array($statusSidang, ['Lulus', 'Revisi']) ? now()->format('Y') : null,
                ]
            );

            RiwayatSidang::updateOrCreate(
                [
                    'hasil_sidang_id' => $hasilSidang->id,
                    'jadwal_sidang_tugas_akhir_id' => $jadwalId,
                ],
                [
                    'hasil_akhir_ta_id' => $hasilAkhir->id,
                    'status_sidang' => $statusSidang,
                ]
            );
        }

        return redirect()->route('penilaian_ta.catatan.form', [
            'mahasiswa_id' => $request->mahasiswa_id,
            'jadwal_sidang_tugas_akhir_id' => $request->jadwal_sidang_tugas_akhir_id,
        ])->with('success', 'Catatan revisi berhasil disimpan.');
    }

    public function cetakRevisiTugasAkhir(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized.');
        }

        $dosenId = $user->dosen->id;
        $jadwalId = $request->query('jadwal_sidang_tugas_akhir_id');
        $mahasiswaId = $request->query('mahasiswa_id');

        $jadwalSidang = JadwalSidangTugasAkhir::with([
            'ruanganSidang',
            'mahasiswa',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping'
        ])->findOrFail($jadwalId);

        // Cek peran dosen
        if ($jadwalSidang->pembimbing_utama_id == $dosenId) {
            $peran = 'pembimbing_utama';
            $dosen = $jadwalSidang->pembimbingUtama;
        } elseif ($jadwalSidang->pembimbing_pendamping_id == $dosenId) {
            $peran = 'pembimbing_pendamping';
            $dosen = $jadwalSidang->pembimbingPendamping;
        } elseif ($jadwalSidang->penguji_utama_id == $dosenId) {
            $peran = 'penguji_utama';
            $dosen = $jadwalSidang->pengujiUtama;
        } elseif ($jadwalSidang->penguji_pendamping_id == $dosenId) {
            $peran = 'penguji_pendamping';
            $dosen = $jadwalSidang->pengujiPendamping;
        } else {
            return abort(403, 'Anda bukan dosen yang terlibat di sidang ini.');
        }

        $catatan = CatatanRevisiTA::where([
            'mahasiswa_id' => $mahasiswaId,
            'dosen_id' => $dosenId,
            'jadwal_sidang_tugas_akhir_id' => $jadwalId,
        ])->first();

        if (!$catatan || !$catatan->catatan_revisi) {
            return back()->with('error', 'Catatan revisi belum tersedia untuk dicetak.');
        }

        $jadwal = $jadwalSidang;
        $pdf = Pdf::loadView('catatan_revisi.cetak_tugas_akhir', compact('catatan', 'jadwal', 'dosen', 'peran'))
            ->setPaper('A4');

        return $pdf->download('Form Revisi Sidang Tugas Akhir ' . $catatan->mahasiswa->nama_mahasiswa . ' Dosen ' . $catatan->dosen->nama_dosen . '.pdf');
    }

    public function gabungRevisi($jadwalSidangId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        // Ambil data sidang berdasarkan ID jadwal
        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'ruanganSidang',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping'
        ])->findOrFail($jadwalSidangId);

        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;
        } elseif ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Super Admin') {
            $mahasiswa = $sidang->mahasiswa;
        } else {
            return abort(403, 'Unauthorized');
        }


        $mahasiswaId = $mahasiswa->id;

        // Urutan peran
        $urutanPeran = [
            'Penguji Utama' => $sidang->penguji_utama_id,
            'Penguji Pendamping' => $sidang->penguji_pendamping_id,
            'Pembimbing Utama' => $sidang->pembimbing_utama_id,
            'Pembimbing Pendamping' => $sidang->pembimbing_pendamping_id,
        ];

        $paths = [];

        foreach ($urutanPeran as $peran => $dosenId) {
            if (!$dosenId) continue;

            $catatan = CatatanRevisiTA::where([
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id' => $dosenId,
                'jadwal_sidang_tugas_akhir_id' => $jadwalSidangId,
            ])->first();

            if (!$catatan || !$catatan->catatan_revisi) continue;

            $dosen = Dosen::find($dosenId);
            $jadwal = $sidang;

            $pdf = Pdf::loadView('catatan_revisi.cetak_tugas_akhir', compact('catatan', 'jadwal', 'dosen', 'peran'))
                ->setPaper('A4');

            File::ensureDirectoryExists(storage_path('app/temp'));
            $filename = "catatan_revisi_{$mahasiswaId}_{$dosenId}.pdf";
            $path = storage_path("app/temp/{$filename}");
            $pdf->save($path);

            $paths[] = $path;
        }

        if (empty($paths)) {
            return back()->with('error', 'Belum ada catatan revisi yang tersedia untuk mahasiswa ini.');
        }

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
        $namaMahasiswa = Str::slug($mahasiswa->nama_mahasiswa);
        $filename = "Catatan_Revisi_TA_{$namaMahasiswa}.pdf";

        return response($output)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }
}
