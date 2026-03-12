<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\LogbookBimbingan;
use App\Models\Mahasiswa;
use App\Models\PendaftaranBimbingan;
use App\Models\PengajuanPembimbing;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');


class LogbookBimbinganController extends Controller
{
    public function indexMahasiswa()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();
        if ($user->role === 'Mahasiswa') {
            $user = Auth::user();
            // $mahasiswa = Auth::user();
            $mahasiswa = $user->mahasiswa;
            $pengajuan = PengajuanPembimbing::where('mahasiswa_id', $mahasiswa->id)->first();
            $logbooks = LogbookBimbingan::all();
        } else {
            abort(403);
        }
        return view('logbook_bimbingan.index_mahasiswa', compact('pengajuan', 'mahasiswa', 'logbooks', 'user'));
    }

    public function showMahasiswa($dosenId, $mahasiswaId)
    {

        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        // Ambil mahasiswa berdasarkan ID
        $user = Auth::user();
        $mahasiswa = Mahasiswa::find($mahasiswaId);
        $proposal = $mahasiswa->proposal;

        if ($user->role === 'Mahasiswa' || $user->role === 'Dosen') {
            if ($user->role == 'Mahasiswa') {
                $mahasiswaId = Auth::user()->mahasiswa->id;
            }

            // Ambil pengajuan pembimbing berdasarkan mahasiswa
            $pengajuan = PengajuanPembimbing::where('mahasiswa_id', $mahasiswaId)->first();

            // Ambil jadwal yang sudah digunakan di tabel logbook_bimbingan
            $usedJadwalIds = LogbookBimbingan::whereHas('pendaftaranBimbingan', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })->pluck('pendaftaran_bimbingan_id');

            $availablePendaftaranBimbingan = PendaftaranBimbingan::where('mahasiswa_id', $mahasiswaId)
                ->where('status_pendaftaran', 'Diterima') // Tambahkan filter ini
                ->whereHas('jadwalBimbingan', function ($query) use ($dosenId) {
                    $query->where('dosen_id', $dosenId);
                })
                ->whereNotIn('id', function ($query) {
                    $query->select('pendaftaran_bimbingan_id')
                        ->from('logbook_bimbingan');
                })
                ->get();

            // Ambil data logbook sesuai dengan role pengguna
            $logbooks = LogbookBimbingan::whereHas('pendaftaranBimbingan', function ($query) use ($mahasiswaId, $dosenId) {
                $query->where('mahasiswa_id', $mahasiswaId)
                    ->where('status_pendaftaran', 'Diterima')
                    ->whereHas('jadwalBimbingan', function ($query) use ($dosenId) {
                        $query->where('dosen_id', $dosenId);
                    });
            })->get();

            $cekLogbook = $logbooks->count() >= 5;
        } else {
            abort(403);
        }

        $dosen = Dosen::find($dosenId);

        return view('logbook_bimbingan.show_mahasiswa', compact(
            'pengajuan',
            'mahasiswa',
            'availablePendaftaranBimbingan',
            'logbooks',
            'cekLogbook',
            'dosen',
            'user',
            'proposal'
        ));
    }

    public function showKaprodi($mahasiswaId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        // Ambil logbook untuk mahasiswa yang dipilih, termasuk relasi dosen
        $logbooks = LogbookBimbingan::with(['pendaftaranBimbingan.jadwalBimbingan.dosen'])
            ->whereHas('pendaftaranBimbingan', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })
            ->get();

        // Ambil data mahasiswa untuk ditampilkan
        $mahasiswa = Mahasiswa::find($mahasiswaId);
        $proposal = $mahasiswa->proposal;

        return view('logbook_bimbingan.show_kaprodi', compact('logbooks', 'mahasiswa', 'user', 'proposal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_bimbingan_id' => 'required|exists:pendaftaran_bimbingan,id', // Validasi yang benar
            'file_bimbingan' => 'required|mimes:pdf|max:10240',
        ]);

        // Ambil pendaftaran_bimbingan
        $pendaftaran = PendaftaranBimbingan::find($request->pendaftaran_bimbingan_id);

        // Pastikan pendaftaran ditemukan
        if (!$pendaftaran) {
            return redirect()->back()->withErrors(['error' => 'Pendaftaran bimbingan tidak ditemukan.']);
        }

        // Ambil mahasiswa_id dari pendaftaran_bimbingan
        $mahasiswa_id = $pendaftaran->mahasiswa_id; // Ambil mahasiswa_id yang berelasi dengan pendaftaran_bimbingan

        $path = null;
        if ($request->hasFile('file_bimbingan')) {
            $file_bimbingan = $request->file('file_bimbingan');
            $filename = time() . '.' . $file_bimbingan->getClientOriginalExtension();
            $file_bimbingan->storeAs('logbooks', $filename, 'public');
            $path = 'logbooks/' . $filename;
        }

        // Simpan logbook
        LogbookBimbingan::create([
            'mahasiswa_id' => $mahasiswa_id, // Gunakan mahasiswa_id yang diambil dari pendaftaran_bimbingan
            'pendaftaran_bimbingan_id' => $request->pendaftaran_bimbingan_id,
            // 'permasalahan' => $permasalahan,
            'file_bimbingan' => $path,
        ]);

        return redirect()->back()->with('success', 'Logbook berhasil ditambahkan.');
    }

    public function view($id)
    {
        $logbook = LogbookBimbingan::findOrFail($id);
        $path = storage_path('app/public/' . $logbook->file_bimbingan);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    }

    public function updatePermasalahan(Request $request, $id)
    {
        // Validasi inputan
        $request->validate([
            'permasalahan' => 'required|string',
        ]);

        // Cari logbook berdasarkan ID
        $logbook = LogbookBimbingan::findOrFail($id);

        // Update kolom permasalahan
        $logbook->update([
            'permasalahan' => $request->permasalahan,
        ]);

        return redirect()->back()->with('success', 'Permasalahan berhasil diperbarui.');
    }

    public function showFile($id)
    {
        $logbook = LogbookBimbingan::findOrFail($id);
        $filePath = storage_path('app/public/' . $logbook->file_bimbingan);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    public function beriRekomendasi($id)
    {
        $user = Auth::user();
        $logbook = LogbookBimbingan::findOrFail($id);

        // Ambil data pengajuan berdasarkan mahasiswa dari logbook
        $pengajuan = PengajuanPembimbing::where('mahasiswa_id', $logbook->mahasiswa_id)->first();

        if (!$pengajuan) {
            return redirect()->back()->with('error', 'Data pengajuan pembimbing tidak ditemukan.');
        }

        if ($user->dosen && $pengajuan->pembimbing_utama_id == $user->dosen->id) {
            $logbook->rekomendasi_utama = 'Ya';
            $logbook->save();
            return back()->with('success', 'Rekomendasi utama berhasil diberikan.');
        } elseif ($user->dosen && $pengajuan->pembimbing_pendamping_id == $user->dosen->id) {
            $logbook->rekomendasi_pendamping = 'Ya';
            $logbook->save();
            return back()->with('success', 'Rekomendasi pendamping berhasil diberikan.');
        }

        return back()->with('error', 'Anda tidak memiliki izin untuk memberikan rekomendasi.');
    }
}
