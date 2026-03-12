<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RuanganSidangController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin')) {

            $ruanganSidang = RuanganSidang::with('programStudi')->paginate(10);
            $programStudi = ProgramStudi::all();
        } else {
            abort(403);
        }

        return view('ruangan_sidang.index', compact('ruanganSidang', 'programStudi', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:50|unique:ruangan_sidang,nama_ruangan',
            'tempat' => 'required|string|max:50',
        ]);

        RuanganSidang::create($request->all());

        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:50|unique:ruangan_sidang,nama_ruangan,' . $id,
            'tempat' => 'required|string|max:50',
        ]);

        $ruanganSidang = RuanganSidang::findOrFail($id);
        $ruanganSidang->update($request->all());

        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil diperbarui');
    }

    public function search(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin')) {

            $programStudi = ProgramStudi::all();
            $search = $request->input('search');

            // Mengambil data pengguna berdasarkan pencarian nama ruangan atau program studi
            $ruanganSidang = RuanganSidang::when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('nama_ruangan', 'like', "%$search%")
                        ->orWhere('tempat', 'like', "%$search%");
                });
            })->paginate(10);
        } else {
            abort(403);
        }

        return view('ruangan_sidang.index', compact('ruanganSidang', 'programStudi', 'user'));
    }

    public function destroy(string $id)
    {
        $ruanganSidang = RuanganSidang::findOrFail($id);
        $ruanganSidang->delete();

        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
