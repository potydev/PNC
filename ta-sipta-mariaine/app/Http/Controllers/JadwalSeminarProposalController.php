<?php

namespace App\Http\Controllers;

use App\Imports\JadwalSeminarProposalImport;
use App\Imports\JadwalSidangTugasAkhirImport;
use App\Models\Dosen;
use App\Models\JadwalSeminarProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');


class JadwalSeminarProposalController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        $jadwals = JadwalSeminarProposal::with([
            'mahasiswa.programStudi',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ])
            ->orderBy('created_at', 'desc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // Group berdasarkan program studi
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama_prodi ?? 'Tidak Diketahui';
        });

        $programStudi = ProgramStudi::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();
        $tahunAjaranList = TahunAjaran::all();

        return view('jadwal_sidang.jadwal_seminar_proposal', compact(
            'jadwals',
            'user',
            'ruanganSidang',
            'pengujiUtama',
            'pengujiPendamping',
            'jadwalsGrouped',
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
            Excel::import(new JadwalSeminarProposalImport, $request->file('file'));
            return redirect()->route('jadwal_seminar_proposal.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'penguji_utama_id' => 'required|exists:dosen,id|different:penguji_pendamping_id',
            'penguji_pendamping_id' => 'required|exists:dosen,id|different:penguji_utama_id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i:s',
            'waktu_selesai' => 'required|date_format:H:i:s|after:waktu_mulai',
            'ruangan_sidang_id' => 'required|exists:ruangan_sidang,id',
        ]);

        $jadwal = JadwalSeminarProposal::findOrFail($id);

        // Poin 1: Cek penguji utama vs pendamping (tidak boleh sama)
        if ($request->penguji_utama_id == $request->penguji_pendamping_id) {
            return back()->withErrors(['error' => 'Penguji utama dan pendamping tidak boleh sama.'])->withInput();
        }

        // Poin 2: Cek bentrok total (tanggal, waktu, ruangan sama)
        $bentrokLangsung = JadwalSeminarProposal::where('id', '!=', $id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_mulai', $request->waktu_mulai)
            ->where('waktu_selesai', $request->waktu_selesai)
            ->where('ruangan_sidang_id', $request->ruangan_sidang_id)
            ->exists();

        if ($bentrokLangsung) {
            return back()->withErrors(['error' => 'Jadwal dengan waktu dan ruangan yang sama sudah terpakai.'])->withInput();
        }

        // Poin 3: Cek konflik penguji (penguji sama di waktu sama meskipun beda ruangan)
        $konflikPenguji = JadwalSeminarProposal::where('id', '!=', $id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_mulai', $request->waktu_mulai)
            ->where('waktu_selesai', $request->waktu_selesai)
            ->where(function ($query) use ($request) {
                $query->where('penguji_utama_id', $request->penguji_utama_id)
                    ->orWhere('penguji_pendamping_id', $request->penguji_utama_id)
                    ->orWhere('penguji_utama_id', $request->penguji_pendamping_id)
                    ->orWhere('penguji_pendamping_id', $request->penguji_pendamping_id);
            })
            ->exists();

        if ($konflikPenguji) {
            return back()->withErrors(['error' => 'Penguji ini sudah terlibat di jadwal lain pada waktu yang sama.'])->withInput();
        }

        // Update
        $jadwal->update($request->all());

        return redirect()->route('jadwal_seminar_proposal.index')->with('success', 'Data jadwal seminar berhasil diperbarui');
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        // Data pendukung untuk dropdown
        $programStudi = ProgramStudi::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();
        $tahunAjaranList = TahunAjaran::all();

        // Ambil input filter
        $programStudiId = $request->input('program_studi');
        $tahunAjaranId = $request->input('tahun_ajaran');
        $dosenId = $request->input('dosen_id');

        // Ambil semua jadwal seminar proposal dan filter sesuai input
        $jadwals = JadwalSeminarProposal::with([
            'mahasiswa.programStudi',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ])
            ->when($programStudiId, function ($query) use ($programStudiId) {
                $query->whereHas('mahasiswa', function ($subQuery) use ($programStudiId) {
                    $subQuery->where('program_studi_id', $programStudiId);
                });
            })
            ->when($tahunAjaranId, function ($query) use ($tahunAjaranId) {
                $query->whereHas('mahasiswa', function ($subQuery) use ($tahunAjaranId) {
                    $subQuery->where('tahun_ajaran_id', $tahunAjaranId);
                });
            })
            ->when($dosenId, function ($query) use ($dosenId) {
                $query->where(function ($q) use ($dosenId) {
                    $q->where('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // Grouping berdasarkan nama prodi
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama_prodi ?? 'Tidak Diketahui';
        });

        return view('jadwal_sidang.jadwal_seminar_proposal', compact(
            'jadwals',
            'jadwalsGrouped',
            'programStudi',
            'tahunAjaranList',
            'user',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ));
    }
}
