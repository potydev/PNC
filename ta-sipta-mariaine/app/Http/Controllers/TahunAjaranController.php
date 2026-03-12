<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TahunAjaranController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin')) {
            $tahunAjaran = TahunAjaran::paginate(10);
        } else {
            abort(403);
        }

        return view('tahun_ajaran.index', compact('tahunAjaran', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9|unique:tahun_ajaran,tahun_ajaran'
        ]);
        TahunAjaran::create($request->all());

        return redirect()->route('tahun_ajaran.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9|unique:tahun_ajaran,tahun_ajaran,' . $id,
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($request->all());

        return redirect()->route('tahun_ajaran.index')->with('success', 'Program Studi berhasil diperbarui');
    }

    public function search(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        $search = $request->input('search');
        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin')) {
            $tahunAjaran = TahunAjaran::when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('tahun_ajaran', 'like', "%$search%");
                });
            })->paginate(10);
        } else {
            abort(403);
        }

        return view('tahun_ajaran.index', compact('tahunAjaran', 'user'));
    }

    public function destroy(string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();

        return redirect()->route('tahun_ajaran.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
