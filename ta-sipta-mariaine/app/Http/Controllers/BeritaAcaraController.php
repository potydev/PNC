<?php

namespace App\Http\Controllers;

use App\Models\HasilAkhirTA;
use App\Models\HasilSidang;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');


use App\Models\JadwalSeminarProposal;
use App\Models\JadwalSidangTugasAkhir;

class BeritaAcaraController extends Controller
{
    public function seminarProposal()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $mahasiswa = Auth::user()->mahasiswa;

        // Ambil jadwal seminar mahasiswa
        $jadwal = JadwalSeminarProposal::with([
            'mahasiswa.proposal',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])->where('mahasiswa_id', $mahasiswa->id)->first();

        if ($jadwal) {
            $nilai = $jadwal->mahasiswa->hasilAkhirSempro;
            $status = $nilai->status_sidang ?? 'Belum Ada';
        } else {
            $nilai = null;
            $status = 'Belum Ada';
        }

        return view('berita_acara.seminar_proposal', compact('jadwal', 'nilai', 'status'));
    }

    public function cetakSeminarProposal(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $mahasiswaId = $request->input('mahasiswa_id');
        $jadwalId = $request->input('jadwal_id');

        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;
        } elseif ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Super Admin') {
            $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        } else {
            return abort(403, 'Unauthorized');
        }

        // Ambil jadwal seminar mahasiswa
        $jadwal = JadwalSeminarProposal::with([
            'mahasiswa.proposal',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])->where('mahasiswa_id', $mahasiswa->id)->first();

        if ($jadwal) {
            $nilai = $jadwal->mahasiswa->hasilAkhirSempro;
            $status = $nilai->status_sidang ?? 'Belum Ada';
        } else {
            $nilai = null;
            $status = 'Belum Ada';
        }

        $pdf = Pdf::loadView('berita_acara.cetak_seminar_proposal', compact('jadwal', 'nilai', 'status'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Berita Acara Seminar Proposal ' . $mahasiswa->nama_mahasiswa . '.pdf');
    }

    public function lihatSeminarProposal(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $mahasiswaId = $request->input('mahasiswa_id');
        $jadwalId = $request->input('jadwal_id');

        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;
        } elseif ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Super Admin') {
            $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        } else {
            return abort(403, 'Unauthorized');
        }

        // Ambil jadwal seminar mahasiswa
        $jadwal = JadwalSeminarProposal::with([
            'mahasiswa.proposal',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])->where('mahasiswa_id', $mahasiswa->id)->first();

        if ($jadwal) {
            $nilai = $jadwal->mahasiswa->hasilAkhirSempro;
            $status = $nilai->status_sidang ?? 'Belum Ada';
        } else {
            $nilai = null;
            $status = 'Belum Ada';
        }

        $pdf = Pdf::loadView('berita_acara.cetak_seminar_proposal', compact('jadwal', 'nilai', 'status'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('Berita Acara Seminar Proposal ' . $mahasiswa->nama_mahasiswa . '.pdf');
    }

    public function sidangTugasAkhir()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Cek apakah mahasiswa sudah punya entri hasil_sidang
        $punyaHasilSidang = HasilSidang::where('mahasiswa_id', $mahasiswa->id)->exists();

        // Kalau belum ada hasil_sidang, tampilkan alert kosong
        if (! $punyaHasilSidang) {
            return view('berita_acara.sidang_tugas_akhir', [
                'dataBeritaAcara' => collect(),
                'hasilSidang' => false,
            ]);
        }

        // Ambil semua jadwal sidang milik mahasiswa
        $riwayatSidang = JadwalSidangTugasAkhir::with([
            'mahasiswa.proposal',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'riwayatSidang'
        ])->where('mahasiswa_id', $mahasiswa->id)->get();

        // 🔄 Siapkan data berita acara
        $dataBeritaAcara = $riwayatSidang->map(function ($jadwal) {
            $judulSidang = $jadwal->jenis_sidang === 'Sidang Ulang'
                ? 'Sidang Ulang Tugas Akhir'
                : 'Sidang Tugas Akhir';

            $hasil_akhir = HasilAkhirTA::where('mahasiswa_id', $jadwal->mahasiswa_id)
                ->where('jadwal_sidang_tugas_akhir_id', $jadwal->id)
                ->first();

            $status = $jadwal->riwayatSidang?->status_sidang ?? 'Belum Ada';

            return [
                'jadwal' => $jadwal,
                'judulSidang' => $judulSidang,
                'hasil_akhir' => $hasil_akhir,
                'status' => $status,
            ];
        });

        return view('berita_acara.sidang_tugas_akhir', [
            'dataBeritaAcara' => $dataBeritaAcara,
            'hasilSidang' => true
        ]);
    }

    public function cetakSidangTugasAkhir(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $mahasiswaId = $request->input('mahasiswa_id');
        $jadwalId = $request->input('jadwal_id');

        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;
        } elseif ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Super Admin') {
            $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        } else {
            return abort(403, 'Unauthorized');
        }

        $jadwal = JadwalSidangTugasAkhir::with([
            'mahasiswa.proposal',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'riwayatSidang',
        ])->where('id', $jadwalId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();


        $jenisSidang = $jadwal->jenis_sidang;

        $judulSidang = $jadwal->jenis_sidang === 'Sidang Ulang'
            ? 'Sidang Ulang Tugas Akhir'
            : 'Sidang Tugas Akhir';

        $hasil_akhir = HasilAkhirTA::where('mahasiswa_id', $jadwal->mahasiswa_id)
            ->where('jadwal_sidang_tugas_akhir_id', $jadwal->id)
            ->first();

        $status = $jadwal->riwayatSidang?->status_sidang ?? 'Belum Ada';

        $pdf = Pdf::loadView('berita_acara.cetak_sidang_tugas_akhir', compact('jadwal', 'judulSidang', 'hasil_akhir', 'status'))
            ->setPaper('A4', 'portrait');

        return $pdf->download(
            'Berita Acara ' . $jenisSidang . ' ' . $mahasiswa->nama_mahasiswa . '.pdf'
        );
    }

    public function lihatSidangTugasAkhir(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        // $jenisSidang = $request->input('jenis_sidang');
        $mahasiswaId = $request->input('mahasiswa_id');
        $jadwalId = $request->input('jadwal_id');

        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;
        } elseif ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Super Admin') {
            $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        } else {
            return abort(403, 'Unauthorized');
        }

        $jadwal = JadwalSidangTugasAkhir::with([
            'mahasiswa.proposal',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'riwayatSidang',
        ])->where('id', $jadwalId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();


        $jenisSidang = $jadwal->jenis_sidang;

        $judulSidang = $jadwal->jenis_sidang === 'Sidang Ulang'
            ? 'Sidang Ulang Tugas Akhir'
            : 'Sidang Tugas Akhir';

        $hasil_akhir = HasilAkhirTA::where('mahasiswa_id', $jadwal->mahasiswa_id)
            ->where('jadwal_sidang_tugas_akhir_id', $jadwal->id)
            ->first();

        $status = $jadwal->riwayatSidang?->status_sidang ?? 'Belum Ada';

        $pdf = Pdf::loadView('berita_acara.cetak_sidang_tugas_akhir', compact('jadwal', 'judulSidang', 'hasil_akhir', 'status'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Berita Acara ' . $jenisSidang . ' ' . $mahasiswa->nama_mahasiswa . '.pdf'
        );
    }

    public function sidangTugasAkhirKaprodi($id)
    {
        $jadwal = JadwalSidangTugasAkhir::with([
            'mahasiswa.proposal',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])->where('mahasiswa_id', $id)->first();

        $nilai = $jadwal->mahasiswa->nilai;

        $status = null;
        if ($nilai && $nilai->nilai_ta !== null) {
            $nilaiTA = $nilai->nilai_ta;
            if ($nilaiTA >= 80) {
                $status = 'Lulus';
            } elseif ($nilaiTA >= 50 && $nilaiTA < 80) {
                $status = 'Revisi';
            } else {
                $status = 'Ditolak';
            }
        }

        return view('berita_acara.sidang_tugas_akhir', compact('jadwal', 'nilai', 'status'));
    }

    public function seminarProposalKaprodi($id)
    {
        $jadwal = JadwalSeminarProposal::with([
            'mahasiswa.proposal',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])->where('mahasiswa_id', $id)->first();

        $nilai = $jadwal->mahasiswa->hasilAkhirSempro;

        $status = null;
        if ($nilai && $nilai->nilai_seminar !== null) {
            $nilaiSeminar = $nilai->nilai_seminar;
            if ($nilaiSeminar >= 80) {
                $status = 'Lulus';
            } elseif ($nilaiSeminar >= 50 && $nilaiSeminar < 80) {
                $status = 'Revisi';
            } else {
                $status = 'Ditolak';
            }
        }

        return view('berita_acara.seminar_proposal', compact('jadwal', 'nilai', 'status'));
    }
}
