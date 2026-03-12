<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\TahunAjaran;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use App\Models\RuanganSidang;
use App\Imports\MahasiswaImport;
use App\Models\Dosen;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');



class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $programStudiId = $user->dosen->program_studi_id;

            $mahasiswa = Mahasiswa::with('user', 'proposal', 'tahunAjaran')
                ->join('tahun_ajaran', 'mahasiswa.tahun_ajaran_id', '=', 'tahun_ajaran.id')
                ->where('program_studi_id', $programStudiId)
                ->orderBy('tahun_ajaran.tahun_ajaran', 'desc')
                ->orderBy('nama_mahasiswa', 'asc')
                ->select('mahasiswa.*') // agar kolom tidak bentrok
                ->paginate(10);
        } elseif ($user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
            $mahasiswa = Mahasiswa::with('user', 'proposal', 'tahunAjaran')
                ->join('tahun_ajaran', 'mahasiswa.tahun_ajaran_id', '=', 'tahun_ajaran.id')
                ->orderBy('tahun_ajaran.tahun_ajaran', 'desc')
                ->orderBy('nama_mahasiswa', 'asc')
                ->select('mahasiswa.*')
                ->paginate(10);
        } else {
            abort(403);
        }

        $programStudi = ProgramStudi::all();
        $tahunAjaran = TahunAjaran::all();

        return view('mahasiswa.index', compact('programStudi', 'tahunAjaran', 'mahasiswa', 'user'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:100|unique:users,name',
            'email' => 'required|string|email|max:100|unique:users,email',
            'nim' => 'required|integer|digits_between:1,9|unique:mahasiswa,nim',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|string|max:9',
            'program_studi_id' => 'required|exists:program_studi,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        // Tentukan nilai default untuk password dan role
        $password = Hash::make('11111111');
        $email_verified_at = now();
        $role = 'Mahasiswa';

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => $email_verified_at,
            'password' => $password,
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simpan data mahasiswa baru dengan user_id dari user yang baru dibuat
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama_mahasiswa' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'program_studi_id' => $request->program_studi_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        if (!$mahasiswa) {
            $user->delete();
            return redirect()->back()->with('error', 'Gagal menambahkan data mahasiswa.');
        }

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // Temukan mahasiswa dan user terkait
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = User::findOrFail($mahasiswa->user_id);

        $request->validate([
            'name' => 'required|string|max:100|unique:users,name,' . $user->id,
            'nim' => 'required|integer|digits_between:1,9|unique:mahasiswa,nim,' . $id,
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|string|max:9',
            'program_studi_id' => 'required|exists:program_studi,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        // Jika mahasiswa login sendiri, validasi upload tanda tangan
        if (Auth::user()->id === $user->id && $request->hasFile('ttd_mahasiswa')) {
            $request->validate([
                'ttd_mahasiswa' => 'image|mimes:jpg,jpeg,png|max:2048',
            ]);
        }

        // Update data user
        $user->update([
            'name' => $request->name,
        ]);

        // Update data mahasiswa
        $mahasiswa->update([
            'nim' => $request->nim,
            'nama_mahasiswa' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'program_studi_id' => $request->program_studi_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        // Simpan tanda tangan jika diunggah oleh mahasiswa sendiri
        if (Auth::user()->id === $user->id && $request->hasFile('ttd_mahasiswa')) {
            $file = $request->file('ttd_mahasiswa');
            $filename = 'ttd_mahasiswa_' . $mahasiswa->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/ttd_mahasiswa', $filename);

            $mahasiswa->update([
                'ttd_mahasiswa' => 'storage/ttd_mahasiswa/' . $filename,
            ]);
        }

        if (request()->routeIs('mahasiswa.profile.update')) {
            // Sedang edit dirinya sendiri
            return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui.');
        } else {
            return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diupdate.');
        }
    }

    public function search(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $search = $request->input('search'); // Ambil input pencarian
        $query = Mahasiswa::with('user', 'proposal');

        // Cek role dan jabatan
        if ($user->role === 'Dosen') {
            $jabatan = $user->dosen->jabatan;

            if ($jabatan === 'Koordinator Program Studi') {
                // Batasi ke program studi kaprodi
                $programStudiId = $user->dosen->program_studi_id;
                $query->where('program_studi_id', $programStudiId);
            } elseif ($jabatan !== 'Super Admin') {
                // Kalau bukan Super Admin juga, tolak akses
                abort(403);
            }
        }

        // Tambahkan kondisi pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_mahasiswa', 'like', "%$search%")
                    ->orWhere('nim', 'like', "%$search%");
                // Tambahkan kolom lain jika perlu
            });
        }

        $mahasiswa = $query->orderBy('nama_mahasiswa', 'asc')->paginate(10);

        $programStudi = ProgramStudi::all();
        $tahunAjaran = TahunAjaran::all();

        return view('mahasiswa.index', compact('mahasiswa', 'programStudi', 'tahunAjaran', 'user'));
    }

    public function dropdownSearch(Request $request)
    {
        $user = Auth::user();
        $programStudi = ProgramStudi::all();
        $tahunAjaran = TahunAjaran::all();

        $mahasiswa = Mahasiswa::with('user', 'proposal', 'tahunAjaran') // pastikan eager load
            ->join('tahun_ajaran', 'mahasiswa.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->when(
                $user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi',
                fn($q) => $q->where('program_studi_id', $user->dosen->program_studi_id),
                fn($q) => $request->filled('program_studi')
                    ? $q->where('program_studi_id', $request->program_studi)
                    : $q
            )
            ->when($request->filled('tahun_ajaran'), fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran))
            ->when($request->filled('jenis_kelamin'), fn($q) => $q->where('jenis_kelamin', $request->jenis_kelamin))
            ->orderBy('tahun_ajaran.tahun_ajaran', 'desc') // urut berdasarkan isi tahun ajaran
            ->orderBy('nama_mahasiswa', 'asc')
            ->select('mahasiswa.*') // agar kolom tidak bentrok
            ->paginate(10);

        return view('mahasiswa.index', compact('mahasiswa', 'programStudi', 'tahunAjaran', 'user'));
    }


    public function destroy(string $id)
    {
        // Temukan user dan mahasiswa yang akan dihapus
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = User::findOrFail($mahasiswa->user_id);
        $mahasiswa->delete();
        $user->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function unggahTTD(Request $request)
    {
        $request->validate([
            'ttd_mahasiswa' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if ($request->hasFile('ttd_mahasiswa')) {
            $file = $request->file('ttd_mahasiswa');
            $filename = 'ttd_' . $mahasiswa->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('ttd_mahasiswa', $filename);

            $mahasiswa->update([
                'ttd_mahasiswa' => 'ttd_mahasiswa/' . $filename
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
            Excel::import(new MahasiswaImport, $request->file('file'));
            return redirect()->route('mahasiswa.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data mahasiswa: ' . $e->getMessage());
        }
    }
}
