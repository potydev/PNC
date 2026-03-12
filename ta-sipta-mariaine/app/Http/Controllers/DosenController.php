<?php

namespace App\Http\Controllers;

use App\Imports\DosenImport;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');

class DosenController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
            $dosen = Dosen::with('user')
                ->orderBy('program_studi_id', 'asc')
                ->orderBy('nama_dosen', 'asc')
                ->paginate(10);
            $programStudi = ProgramStudi::all();
        } else {
            abort(403);
        }

        return view('dosen.index', compact('dosen', 'programStudi', 'user'));
    }

    public function store(Request $request)
    {
        // Validasi awal
        $request->validate([
            'name' => 'required|string|max:100|unique:users,name',
            'email' => 'required|string|email|max:100|unique:users,email',
            'nip' => 'required|integer|digits_between:1,50|unique:dosen,nip',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|string|max:9',
            'jabatan' => 'required|in:Dosen Biasa,Koordinator Program Studi,Super Admin',
            'program_studi_id' => 'required|exists:program_studi,id',
        ]);

        // Cek jika jabatan adalah Kaprodi, pastikan belum ada Kaprodi di prodi tersebut
        if ($request->jabatan === 'Koordinator Program Studi') {
            $kaprodiSudahAda = Dosen::where('jabatan', 'Koordinator Program Studi')
                ->where('program_studi_id', $request->program_studi_id)
                ->exists();

            if ($kaprodiSudahAda) {
                return back()->with('error', 'Sudah ada Kaprodi untuk program studi yang dipilih.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make('11111111'),
            'role' => 'Dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama_dosen' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'jabatan' => $request->jabatan,
            'program_studi_id' => $request->program_studi_id,

        ]);

        if (!$dosen) {
            $user->delete();
            return redirect()->back()->with('error', 'Gagal menambahkan data dosen.');
        }

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);
        $user = User::findOrFail($dosen->user_id);

        $request->validate([
            'name' => 'required|string|max:100|unique:users,name,' . $user->id,
            'nip' => 'required|integer|digits_between:1,50|unique:dosen,nip,' . $id,
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|string|max:9',
            'jabatan' => 'required|in:Dosen Biasa,Koordinator Program Studi,Super Admin',
            'program_studi_id' => 'required|exists:program_studi,id',
        ]);

        // Jika jabatan Koordinator Program Studi, cek agar tidak ada dua kaprodi untuk satu prodi
        if ($request->jabatan === 'Koordinator Program Studi') {
            $kaprodiSudahAda = Dosen::where('jabatan', 'Koordinator Program Studi')
                ->where('program_studi_id', $request->program_studi_id)
                ->where('id', '!=', $dosen->id)
                ->exists();

            if ($kaprodiSudahAda) {
                return back()->with('error', 'Sudah ada Kaprodi untuk program studi yang dipilih.');
            }
        }


        $user->update([
            'name' => $request->name,
        ]);

        $dosen->update([
            'nip' => $request->nip,
            'nama_dosen' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'jabatan' => $request->jabatan,
            'program_studi_id' => $request->program_studi_id,
        ]);

        if (request()->routeIs('dosen.profile.update')) {
            return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui.');
        }

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diupdate.');
    }

    public function search(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $programStudi = ProgramStudi::all();
        $search = $request->input('search');

        // Mengambil data dosen berdasarkan pencarian
        $dosen = Dosen::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_dosen', 'like', "%$search%")
                    ->orWhere('nip', 'like', "%$search%")
                    ->orWhere('tempat_lahir', 'like', "%$search%")
                    ->orWhere('jenis_kelamin', 'like', "%$search%");
            });
        })->orderBy('nama_dosen', 'asc')
            ->paginate(10);

        // Mengirimkan data ke view
        return view('dosen.index', compact('dosen', 'programStudi', 'user'));
    }

    public function destroy(string $id)
    {
        // Temukan user dan mahasiswa yang akan dihapus
        $dosen = Dosen::findOrFail($id);
        $user = User::findOrFail($dosen->user_id);
        $dosen->delete();
        $user->delete();

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus.');
    }

    public function mahasiswaBimbingan()
    {
        $dosenId = Auth::id();

        // Ambil data mahasiswa yang mengajukan dosen ini sebagai pembimbing utama atau pendamping
        $mahasiswa = Mahasiswa::whereHas('pengajuanPembimbing', function ($query) use ($dosenId) {
            $query->where('pembimbing_utama_id', $dosenId)
                ->orWhere('pembimbing_pendamping_id', $dosenId);
        })->get();

        return view('dosen.mahasiswa_pengajuan_bimbingan', compact('mahasiswa'));
    }

    public function unggahTTD(Request $request)
    {
        $request->validate([
            'ttd_dosen' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $dosen = $user->dosen;

        if ($request->hasFile('ttd_dosen')) {
            $file = $request->file('ttd_dosen');
            $filename = 'ttd_' . $dosen->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('ttd_dosen', $filename);

            $dosen->update([
                'ttd_dosen' => 'ttd_dosen/' . $filename
            ]);
        }

        return redirect()->back()->with('success', 'Tanda tangan berhasil diunggah.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new DosenImport, $request->file('file'));
            return redirect()->route('dosen.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }
}
