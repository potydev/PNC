<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
            $user = User::orderBy('role', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('name', 'asc')->paginate(10);
        } else {
            abort(403);
        }

        return view('user.index', compact('user', 'user'));
    }

    public function search(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $search = $request->input('search');

        $user = Auth::user();
        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
            // Mengambil data pengguna berdasarkan pencarian nama, email, atau role
            $user = User::when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('role', 'like', "%$search%");
                });
            })->orderBy('role', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('name', 'asc')->paginate(10);
        } else {
            abort(403);
        }

        return view('user.index', compact('user', 'user'));
    }

    public function resetPassword(User $user)
    {
        $user->password = Hash::make('11111111');
        $user->save();

        return redirect()->route('user.index')->with('success', 'Password berhasil direset ke default.');
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {

            $user = User::all();
            // Ambil nilai dari dropdown
            $role = $request->input('role');

            // Query untuk mencari mahasiswa dengan kondisi yang dipilih
            $user = User::when($role, function ($query) use ($role) {
                return $query->where('role', $role);
            })
                ->orderBy('role', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('name', 'asc')->paginate(10);;
        } else {
            abort(403);
        }

        return view('user.index', compact('user', 'role', 'user'));
    }
}
