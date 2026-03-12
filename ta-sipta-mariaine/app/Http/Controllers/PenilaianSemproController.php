<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Models\PenilaianSempro;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilAkhirSempro;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSeminarProposal;

\Carbon\Carbon::setLocale('id');


class PenilaianSemproController extends Controller
{
    public function indexProposalDosen()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $dosenId = Auth::user()->dosen->id;

            $mahasiswa = Mahasiswa::with('jadwalSeminarProposal')->get();

            $seminar = JadwalSeminarProposal::with([
                'mahasiswa',
                'mahasiswa.penilaianSempro',
                'mahasiswa.proposal'
            ])
                ->where(function ($query) use ($dosenId) {
                    $query->where('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                })
                ->orderBy('tanggal', 'desc')->get();
        } elseif ($user->role === 'Dosen') {
            $dosenId = Auth::user()->dosen->id;

            $mahasiswa = Mahasiswa::with('jadwalSeminarProposal')->get();

            $seminar = JadwalSeminarProposal::with([
                'mahasiswa',
                'mahasiswa.penilaianSempro',
                'mahasiswa.proposal'
            ])
                ->where(function ($query) use ($dosenId) {
                    $query->where('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                })
                ->orderBy('tanggal', 'desc')->get();
        }

        return view('penilaian.proposal', compact('seminar', 'dosenId', 'mahasiswa', 'user'));
    }

    public function daftarNilai()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        $nilai = HasilAkhirSempro::with('mahasiswa')->get();
        return view('penilaian.index', compact('nilai', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'dosen_id' => 'required|exists:dosen,id',
            'jadwal_seminar_proposal_id' => 'required|exists:jadwal_seminar_proposal,id',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $mahasiswaId = $request->mahasiswa_id;
        $dosenId = $request->dosen_id;
        $jadwalId = $request->jadwal_seminar_proposal_id;
        $nilai = $request->nilai;

        $jadwal = JadwalSeminarProposal::find($jadwalId);

        if (!$jadwal || ($jadwal->penguji_utama_id != $dosenId && $jadwal->penguji_pendamping_id != $dosenId)) {
            return redirect()->back()->with('error', 'Anda bukan penguji di seminar ini.');
        }

        $peran = $jadwal->penguji_utama_id == $dosenId ? 'penguji_utama' : 'penguji_pendamping';

        // Simpan ke penilaian_sempro
        PenilaianSempro::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id' => $dosenId,
                'jadwal_seminar_proposal_id' => $jadwalId,
            ],
            [
                'nilai' => $nilai,
            ]
        );

        // Simpan/Update ke hasil_akhir_sempro
        $hasil = HasilAkhirSempro::firstOrNew([
            'mahasiswa_id' => $mahasiswaId,
            'jadwal_seminar_proposal_id' => $jadwalId,
        ]);

        if ($peran === 'penguji_utama') {
            $hasil->nilai_penguji_utama = $nilai;
        } elseif ($peran === 'penguji_pendamping') {
            $hasil->nilai_penguji_pendamping = $nilai;
        }

        // Jika kedua nilai sudah terisi, hitung total akhir dan status
        if (!is_null($hasil->nilai_penguji_utama) && !is_null($hasil->nilai_penguji_pendamping)) {
            $hasil->total_akhir = round(($hasil->nilai_penguji_utama + $hasil->nilai_penguji_pendamping) / 2, 2);

            // Cek apakah sudah ada catatan revisi dari salah satu dosen
            $punyaCatatanRevisi = PenilaianSempro::where('mahasiswa_id', $mahasiswaId)
                ->where('jadwal_seminar_proposal_id', $jadwalId)
                ->whereNotNull('catatan_revisi')
                ->exists();

            // Tentukan status berdasarkan ada/tidaknya catatan dan nilai
            if (!$punyaCatatanRevisi) {
                $hasil->status_sidang = 'Lulus';
            } else {
                if ($hasil->total_akhir < 50) {
                    $hasil->status_sidang = 'Ditolak';
                } elseif ($hasil->total_akhir <= 100) {
                    $hasil->status_sidang = 'Revisi';
                } else {
                    $hasil->status_sidang = 'Tidak Valid';
                }
            }
        }

        $hasil->save();

        return redirect()->back()->with('success', 'Nilai seminar proposal berhasil disimpan.');
    }

    public function formTambahCatatan(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized.');
        }

        $dosenId = $user->dosen->id;
        $jadwalId = $request->query('jadwal_seminar_proposal_id');
        $mahasiswaId = $request->query('mahasiswa_id');

        $jadwal = JadwalSeminarProposal::with([
            'ruanganSidang',
            'mahasiswa',
            'pengujiUtama',
            'pengujiPendamping'
        ])->findOrFail($jadwalId);

        // Cek peran dosen di jadwal
        if ($jadwal->penguji_utama_id == $dosenId) {
            $peran = 'penguji_utama';
            $dosen = $jadwal->pengujiUtama;
        } elseif ($jadwal->penguji_pendamping_id == $dosenId) {
            $peran = 'penguji_pendamping';
            $dosen = $jadwal->pengujiPendamping;
        } else {
            return abort(403, 'Anda bukan dosen yang terlibat di seminar ini.');
        }

        $penilaian = PenilaianSempro::firstOrNew([
            'mahasiswa_id' => $mahasiswaId,
            'dosen_id' => $dosenId,
            'jadwal_seminar_proposal_id' => $jadwalId,
        ]);

        return view('catatan_revisi.form_seminar_proposal', compact('penilaian', 'jadwal', 'dosen', 'peran'));
    }

    public function simpanCatatan(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'jadwal_seminar_proposal_id' => 'required|exists:jadwal_seminar_proposal,id',
            'catatan_revisi' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized.');
        }

        $dosenId = $user->dosen->id;
        $mahasiswaId = $request->mahasiswa_id;
        $jadwalId = $request->jadwal_seminar_proposal_id;

        // Simpan/update catatan revisi di penilaian_sempro
        PenilaianSempro::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id' => $dosenId,
                'jadwal_seminar_proposal_id' => $jadwalId,
            ],
            [
                'catatan_revisi' => $request->catatan_revisi,
            ]
        );

        // Cek apakah kedua nilai sudah ada
        $hasil = HasilAkhirSempro::where('mahasiswa_id', $mahasiswaId)
            ->where('jadwal_seminar_proposal_id', $jadwalId)
            ->first();

        if ($hasil && !is_null($hasil->nilai_penguji_utama) && !is_null($hasil->nilai_penguji_pendamping)) {
            // Hitung ulang total_akhir
            $hasil->total_akhir = round(($hasil->nilai_penguji_utama + $hasil->nilai_penguji_pendamping) / 2, 2);

            // Karena sudah ada catatan revisi, sesuaikan status_sidang
            if ($hasil->total_akhir < 50) {
                $hasil->status_sidang = 'Ditolak';
            } elseif ($hasil->total_akhir <= 100) {
                $hasil->status_sidang = 'Revisi';
            } else {
                $hasil->status_sidang = 'Tidak Valid';
            }

            $hasil->save();
        }

        return redirect()->route('penilaian_sempro.catatan.form', [
            'mahasiswa_id' => $mahasiswaId,
            'jadwal_seminar_proposal_id' => $jadwalId,
        ])->with('success', 'Catatan revisi berhasil disimpan.');
    }

    public function cetakFormRevisiSempro(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized.');
        }

        $dosenId = $user->dosen->id;
        $jadwalId = $request->query('jadwal_seminar_proposal_id');
        $mahasiswaId = $request->query('mahasiswa_id');

        $jadwal = JadwalSeminarProposal::with([
            'ruanganSidang',
            'mahasiswa',
            'pengujiUtama',
            'pengujiPendamping'
        ])->findOrFail($jadwalId);

        // Cek peran dosen
        if ($jadwal->penguji_utama_id == $dosenId) {
            $peran = 'penguji_utama';
            $dosen = $jadwal->pengujiUtama;
        } elseif ($jadwal->penguji_pendamping_id == $dosenId) {
            $peran = 'penguji_pendamping';
            $dosen = $jadwal->pengujiPendamping;
        } else {
            return abort(403, 'Anda bukan dosen yang terlibat di seminar ini.');
        }

        $penilaian = PenilaianSempro::firstOrNew([
            'mahasiswa_id' => $mahasiswaId,
            'dosen_id' => $dosenId,
            'jadwal_seminar_proposal_id' => $jadwalId,
        ]);

        $pdf = Pdf::loadView('catatan_revisi.cetak_seminar_proposal', compact('penilaian', 'jadwal', 'dosen', 'peran'))
            ->setPaper('A4');

        return $pdf->download('Form Revisi Seminar Proposal ' . $penilaian->mahasiswa->nama_mahasiswa . ' Dosen ' . $penilaian->dosen->nama_dosen . '.pdf');
    }

    public function gabungRevisiSempro($jadwalId)
    {
        $jadwal = JadwalSeminarProposal::with([
            'ruanganSidang',
            'mahasiswa',
            'pengujiUtama',
            'pengujiPendamping'
        ])->findOrFail($jadwalId);

        $mahasiswa = $jadwal->mahasiswa;
        $mahasiswaId = $mahasiswa->id;

        $urutanPeran = [
            'Penguji Utama' => $jadwal->penguji_utama_id,
            'Penguji Pendamping' => $jadwal->penguji_pendamping_id,
        ];

        $paths = [];

        foreach ($urutanPeran as $peran => $dosenId) {
            if (!$dosenId) continue;

            $catatan = PenilaianSempro::where([
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id' => $dosenId,
                'jadwal_seminar_proposal_id' => $jadwalId,
            ])->first();

            if (!$catatan || !$catatan->catatan_revisi) continue;

            $dosen = Dosen::find($dosenId);

            $pdf = Pdf::loadView('catatan_revisi.cetak_seminar_proposal', [
                'penilaian' => $catatan,
                'jadwal' => $jadwal,
                'dosen' => $dosen,
                'peran' => $peran
            ])->setPaper('A4');

            File::ensureDirectoryExists(storage_path('app/temp'));

            $filename = "catatan_revisi_sempro_{$mahasiswaId}_{$dosenId}.pdf";
            $path = storage_path("app/temp/{$filename}");
            $pdf->save($path);

            $paths[] = $path;
        }

        if (empty($paths)) {
            return back()->with('error', 'Belum ada catatan revisi seminar proposal untuk mahasiswa ini.');
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

        $namaMahasiswa = Str::slug($mahasiswa->nama_mahasiswa);
        $filename = "Catatan_Revisi_Sempro_{$namaMahasiswa}.pdf";

        return response($gabung->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }
}
