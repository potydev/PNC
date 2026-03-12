<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\RubrikNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');

class RubrikNilaiController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();
        $dosen = $user->dosen;

        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi')) {
            $rubrikNilai = RubrikNilai::where('program_studi_id', $dosen->program_studi_id)
                ->orderByRaw("FIELD(jenis_dosen, 'Penguji Utama', 'Penguji Pendamping', 'Pembimbing Utama', 'Pembimbing Pendamping')")
                ->paginate(10);
        } elseif ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Super Admin')) {
            $rubrikNilai = RubrikNilai::with('programStudi')
                ->orderBy('program_studi_id')
                ->orderByRaw("FIELD(jenis_dosen, 'Penguji Utama', 'Penguji Pendamping', 'Pembimbing Utama', 'Pembimbing Pendamping')")
                ->paginate(10);
        } else {
            abort(403);
        }

        $totalPerKategori = RubrikNilai::selectRaw('jenis_dosen, SUM(persentase) as total')
            ->where('program_studi_id', $dosen->program_studi_id)
            ->groupBy('jenis_dosen')
            ->pluck('total', 'jenis_dosen');

        $programStudiList = ProgramStudi::all();

        return view('rubrik_nilai.index', compact('rubrikNilai', 'user', 'totalPerKategori', 'programStudiList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $programStudiId = $user->dosen->program_studi_id;

        $request->validate([
            'jenis_dosen' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:50',
            'kategori' => 'required|string|max:100',
            'persentase' => 'required|integer|min:1|max:100',
        ]);

        // Map untuk split 1 input ke 2 jenis dosen
        $jenisDosenMap = [
            'Penguji' => ['Penguji Utama', 'Penguji Pendamping'],
            'Pembimbing' => ['Pembimbing Utama', 'Pembimbing Pendamping'],
        ];

        $targetJenisList = $jenisDosenMap[$request->jenis_dosen] ?? [$request->jenis_dosen];

        foreach ($targetJenisList as $jenis) {
            $existingTotal = RubrikNilai::where('jenis_dosen', $jenis)
                ->where('program_studi_id', $programStudiId)
                ->sum('persentase');

            if ($existingTotal + $request->persentase > 100) {
                return back()->withInput()->withErrors([
                    'persentase' => "Total persentase untuk '$jenis' sudah mencapai batas 100%."
                ]);
            }

            RubrikNilai::create([
                'program_studi_id' => $programStudiId,
                'jenis_dosen' => $jenis,
                'kelompok' => $request->kelompok,
                'kategori' => $request->kategori,
                'persentase' => $request->persentase,
            ]);
        }

        return redirect()->route('rubrik_nilai.index')->with('success', 'Rubrik Nilai berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'jenis_dosen' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:50',
            'kategori' => 'required|string|max:100',
            'persentase' => 'required|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $programStudiId = $user->dosen->program_studi_id;

        $groupedJenisDosen = [
            'Penguji Utama' => ['Penguji Utama', 'Penguji Pendamping'],
            'Penguji Pendamping' => ['Penguji Utama', 'Penguji Pendamping'],
            'Pembimbing Utama' => ['Pembimbing Utama', 'Pembimbing Pendamping'],
            'Pembimbing Pendamping' => ['Pembimbing Utama', 'Pembimbing Pendamping'],
        ];

        $editSemua = $groupedJenisDosen[$request->jenis_dosen] ?? [$request->jenis_dosen];

        $totalUpdate = $request->persentase * count($editSemua);

        if ($totalUpdate > 100) {
            return back()->withInput()->withErrors([
                'persentase' => 'Total persentase untuk grup ini tidak boleh lebih dari 100%.'
            ]);
        }

        RubrikNilai::where('program_studi_id', $programStudiId)
            ->whereIn('jenis_dosen', $editSemua)
            ->where('kategori', $request->kategori)
            ->update([
                'kelompok' => $request->kelompok,
                'persentase' => $request->persentase,
            ]);

        return redirect()->route('rubrik_nilai.index')->with('success', 'Rubrik Nilai berhasil diperbarui.');
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $programStudiList = ProgramStudi::all();

        if ($user->role !== 'Dosen' || !in_array($user->dosen->jabatan, ['Koordinator Program Studi', 'Super Admin'])) {
            abort(403);
        }

        // Ambil nilai filter dari request
        $jenisDosen = $request->input('jenis_dosen');
        $programStudiId = $request->input('program_studi_id');

        // Siapkan query dasar
        $query = RubrikNilai::query();

        // Filter berdasarkan program studi
        if ($dosen->jabatan !== 'Super Admin') {
            // Kaprodi: hanya boleh melihat prodi miliknya
            $query->where('program_studi_id', $dosen->program_studi_id);
        } elseif ($programStudiId) {
            // Super Admin: jika memilih prodi tertentu
            $query->where('program_studi_id', $programStudiId);
        }

        if ($jenisDosen) {
            $query->where('jenis_dosen', $jenisDosen);
        }

        $rubrikNilai = $query->orderBy('jenis_dosen')->paginate(10);

        $totalPerKategori = RubrikNilai::selectRaw('jenis_dosen, SUM(persentase) as total')
            ->where('program_studi_id', $dosen->program_studi_id)
            ->groupBy('jenis_dosen')
            ->pluck('total', 'jenis_dosen');

        return view('rubrik_nilai.index', compact('rubrikNilai', 'jenisDosen', 'user', 'totalPerKategori', 'programStudiList'));
    }

    public function destroy(string $id)
    {
        $rubrikNilai = RubrikNilai::findOrFail($id);
        $rubrikNilai->delete();
        return redirect()->route('rubrik_nilai.index')->with('success', 'Rubrik Nilai berhasil dihapus');
    }
}
