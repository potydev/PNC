<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        $userCount = User::count();
        $dosenCount = Dosen::count();
        $programstudiCount = ProgramStudi::count();

        if ($user->role === 'Dosen') {
            $dosen = Dosen::where('user_id', $user->id)->first();

            // Default: jumlah semua mahasiswa
            $mahasiswaCount = Mahasiswa::count();

            // Jika dosen adalah Koordinator Program Studi
            if ($dosen && $dosen->jabatan === 'Koordinator Program Studi') {
                $mahasiswaCount = Mahasiswa::where('program_studi_id', $dosen->program_studi_id)->count();
            }

            return view('dashboard.index', compact(
                'userCount',
                'mahasiswaCount',
                'dosenCount',
                'programstudiCount',
                'user',
                'dosen',
            ));
        } elseif ($user->role === 'Mahasiswa') {
            $mahasiswa = Mahasiswa::with(['programStudi', 'tahunAjaran'])
                ->where('user_id', $user->id)->first();

            $programStudi = ProgramStudi::all();
            $tahunAjaran = TahunAjaran::all();

            $mahasiswaCount = Mahasiswa::count(); // untuk tampilan mahasiswa (boleh juga dihilangkan kalau tidak dipakai)

            return view('dashboard.index', compact(
                'userCount',
                'mahasiswaCount',
                'dosenCount',
                'programstudiCount',
                'user',
                'mahasiswa',
                'programStudi',
                'tahunAjaran'
            ));
        } elseif ($user->role === 'Super Admin') {
            $mahasiswaCount = Mahasiswa::count(); // semua mahasiswa
            return view('dashboard.index', compact(
                'userCount',
                'mahasiswaCount',
                'dosenCount',
                'programstudiCount',
                'user'
            ));
        } else {
            abort(403);
        }
    }
}
