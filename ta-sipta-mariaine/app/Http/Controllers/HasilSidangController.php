<?php

namespace App\Http\Controllers;

use App\Models\CatatanRevisiTA;
use App\Models\HasilSidang;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\RiwayatSidang;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');

class HasilSidangController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();
        $dosen = $user->dosen;

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $hasilSidang = HasilSidang::whereHas('mahasiswa', function ($query) use ($dosen) {
                $query->where('program_studi_id', $dosen->program_studi_id);
            })
                ->orderBy('created_at', 'desc') // Tambahkan ini
                ->paginate(10);
            // Hitung jumlah mahasiswa di prodi yang sudah sidang
            $jumlahMahasiswaSidang = HasilSidang::whereHas('mahasiswa', function ($query) use ($dosen) {
                $query->where('program_studi_id', $dosen->program_studi_id);
            })->distinct('mahasiswa_id')->count('mahasiswa_id');
        } elseif ($user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
            $hasilSidang = HasilSidang::with('mahasiswa')
                ->orderBy('created_at', 'desc') // Tambahkan ini
                ->paginate(10);
            $jumlahMahasiswaSidang = HasilSidang::distinct('mahasiswa_id')->count('mahasiswa_id');
        } else {
            abort(403);
        }

        $statusList = HasilSidang::select('status_kelulusan')->distinct()->pluck('status_kelulusan')->filter()->unique()->values();
        $tahunList = HasilSidang::select('tahun_lulus')->distinct()->pluck('tahun_lulus')->filter()->unique()->values();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get(); // untuk dropdown

        return view('hasil_sidang.sidang_tugas_akhir', compact('user', 'hasilSidang', 'statusList', 'tahunList', 'tahunAjaranList', 'jumlahMahasiswaSidang'));
    }

    public function show($id)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin')) {
            // Ambil hasil sidang dan riwayatnya
            $hasilSidang = HasilSidang::with(['mahasiswa', 'riwayatSidang.jadwalSidangTugasAkhir'])->findOrFail($id);

            // Cek apakah mahasiswa punya catatan revisi TA
            foreach ($hasilSidang->riwayatSidang as $riwayat) {
                $riwayat->punya_catatan_revisi_ta = CatatanRevisiTA::where('mahasiswa_id', $hasilSidang->mahasiswa->id)
                    ->where('jadwal_sidang_tugas_akhir_id', $riwayat->jadwal_sidang_tugas_akhir_id)
                    ->exists();
            }

            return view('hasil_sidang.show_riwayat', compact('hasilSidang'));
        } else {
            abort(404);
        }
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        $status = $request->input('status_kelulusan');
        $tahun = $request->input('tahun_lulus');
        $tahunAjaran = $request->input('tahun_ajaran');

        $query = HasilSidang::with('mahasiswa')
            ->when($status, fn($q) => $q->where('status_kelulusan', $status))
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->when($tahunAjaran, function ($query) use ($tahunAjaran) {
                $query->whereHas('mahasiswa', function ($q) use ($tahunAjaran) {
                    $q->where('tahun_ajaran_id', $tahunAjaran);
                });
            });

        $hasilSidang = $query->orderBy('created_at', 'desc')->paginate(10);
        $jumlahMahasiswaSidang = $query->get()->pluck('mahasiswa_id')->unique()->count();
        $statusList = HasilSidang::select('status_kelulusan')->distinct()->pluck('status_kelulusan')->filter()->unique()->values();
        $tahunList = HasilSidang::select('tahun_lulus')->distinct()->pluck('tahun_lulus')->filter()->unique()->values();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get(); // untuk dropdown

        return view('hasil_sidang.sidang_tugas_akhir', compact('user', 'hasilSidang', 'statusList', 'tahunList', 'tahunAjaranList', 'jumlahMahasiswaSidang'));
    }

    public function indexMahasiswa()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Mahasiswa tidak ditemukan.');
        }

        // Ambil semua ID jadwal sidang yang milik mahasiswa ini
        $jadwalIds = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswa->id)->pluck('id');

        // Ambil riwayat sidang berdasarkan jadwal_id tadi
        $riwayatSidang = RiwayatSidang::with(['jadwalSidangTugasAkhir.ruanganSidang',])
            ->whereIn('jadwal_sidang_tugas_akhir_id', $jadwalIds)
            ->get();
        $mahasiswa = $user->mahasiswa;

        // Tambahkan properti untuk menandai apakah punya catatan revisi
        foreach ($riwayatSidang as $riwayat) {
            $riwayat->punya_catatan_revisi_ta = CatatanRevisiTA::where('mahasiswa_id', $mahasiswa->id)
                ->where('jadwal_sidang_tugas_akhir_id', $riwayat->jadwal_sidang_tugas_akhir_id)
                ->exists();
        }
        $hasilSidang = HasilSidang::where('mahasiswa_id', $mahasiswa->id)->first();
        return view('hasil_sidang.index_mahasiswa', compact('riwayatSidang', 'hasilSidang'));
    }

    public function lihatRevisi($jadwalId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        $mahasiswaId = $user->mahasiswa->id;

        $catatanRevisi = CatatanRevisiTA::with('dosen')
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
            ->get();

        return view('catatan_revisi.index_mahasiswa', compact('catatanRevisi'));
    }

    public function uploadRevisi(Request $request, $id)
    {
        $request->validate([
            'file_revisi' => 'required|mimes:pdf|max:20480',
        ]);

        $hasilSidang = HasilSidang::findOrFail($id);

        if ($request->hasFile('file_revisi')) {
            $file = $request->file('file_revisi');

            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('revisi_ta', $filename, 'public');

            $path = 'revisi_ta/' . $filename;
        }
        $hasilSidang->file_revisi = $path;
        $hasilSidang->tanggal_revisi = now();
        $hasilSidang->save();

        return redirect()->back()->with('success', 'File revisi final berhasil diunggah.');
    }

    public function showFileRevisi($id)
    {
        $hasil = HasilSidang::findOrFail($id);
        $filePath = storage_path('app/public/' . $hasil->file_revisi);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    public function cekKelengkapan(Request $request, $id)
    {
        $hasil = HasilSidang::findOrFail($id);
        $hasil->kelengkapan_yudisium = $request->input('kelengkapan_yudisium');
        $hasil->save();

        return redirect()->back()->with('success', 'Kelengkapan yudisium berhasil diperbarui.');
    }
}
