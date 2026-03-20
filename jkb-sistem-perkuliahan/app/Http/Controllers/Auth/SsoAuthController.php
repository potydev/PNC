<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SsoAuthController extends Controller
{
    /**
     * Redirect user ke endpoint authorize SSO.
     *
     * Proses ini membuat `state` acak lalu menyimpannya di session
     * untuk validasi anti-CSRF saat callback dari SSO.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        $query = http_build_query([
            'client_id'    => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'state'        => $state,
        ]);

        return redirect()->away(rtrim(config('sso.base_url'), '/').'/authorize?'.$query);
    }

    /**
     * Tangani callback dari SSO setelah user berhasil autentikasi.
     *
     * Ringkasan alur:
     * 1) Validasi `code` dan `state`
     * 2) Tukar `code` menjadi access token
     * 3) Ambil profil user dari endpoint userinfo
     * 4) Sinkronkan user lokal + role
     * 5) Login ke aplikasi dan redirect sesuai role
     */
    public function callback(Request $request): RedirectResponse
    {
        // Wajib ada code & state dari SSO
        if (! $request->filled('code') || ! $request->filled('state')) {
            return redirect()->route('login')->withErrors(['sso' => 'SSO callback tidak valid.']);
        }

        // Validasi state terhadap session untuk mencegah CSRF
        if ($request->session()->pull('sso_state') !== $request->string('state')->toString()) {
            return redirect()->route('login')->withErrors(['sso' => 'State SSO tidak cocok.']);
        }

        // --- Tukar code dengan access token ---
        $tokenResponse = Http::asForm()
            ->timeout(10)
            ->post(rtrim(config('sso.base_url'), '/').'/token', [
                'grant_type'    => 'authorization_code',
                'code'          => $request->string('code')->toString(),
                'client_id'     => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
                'redirect_uri'  => config('sso.redirect_uri'),
            ]);

        if ($tokenResponse->failed()) {
            return redirect()->route('login')->withErrors(['sso' => 'Gagal menukar code SSO ke token.']);
        }

        // Ambil token akses untuk request userinfo
        $accessToken = $tokenResponse->json('access_token');
        if (! is_string($accessToken) || $accessToken === '') {
            return redirect()->route('login')->withErrors(['sso' => 'Token SSO tidak valid.']);
        }

        // --- Ambil profil user dari SSO ---
        $userInfoResponse = Http::withToken($accessToken)
            ->timeout(10)
            ->get(rtrim(config('sso.base_url'), '/').'/userinfo');

        if ($userInfoResponse->failed()) {
            return redirect()->route('login')->withErrors(['sso' => 'Gagal mengambil profil user SSO.']);
        }

        // Parsing data profil dari SSO
        $profile  = $userInfoResponse->json();
        $email    = data_get($profile, 'email');

        if (! is_string($email) || $email === '') {
            return redirect()->route('login')->withErrors(['sso' => 'Email user SSO tidak ditemukan.']);
        }

        $name     = (string) data_get($profile, 'name', Str::before($email, '@'));
        $roleName = data_get($profile, 'role');

        // --- Buat atau ambil user lokal ---
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make(Str::random(40)),
            ]
        );

        // Perbarui nama jika berubah di SSO
        if ($user->name !== $name) {
            $user->update(['name' => $name]);
        }

        // Assign role dari SSO hanya jika user lokal belum memiliki role
        if (is_string($roleName) && $roleName !== '' && method_exists($user, 'assignRole')) {
            $roleExists = Role::where('name', $roleName)->exists();
            if ($roleExists && $user->roles()->count() === 0) {
                $user->assignRole($roleName);
            }
        }

        // User tanpa role tidak diizinkan masuk ke aplikasi
        $user->refresh();
        if ($user->roles()->count() === 0) {
            return redirect()->route('login')->withErrors([
                'sso' => "Akun {$email} belum terdaftar di sistem. Hubungi administrator untuk mendapatkan akses.",
            ]);
        }

        // Buat sesi login aplikasi lokal (Laravel session)
        Auth::login($user, true);
        $request->session()->regenerate();

        // --- Redirect berdasarkan role ---
        return $this->redirectByRole($user);
    }

    /**
     * Arahkan user ke halaman yang sesuai berdasarkan role-nya.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        // Super admin masuk ke dashboard utama
        if ($user->hasRole('super_admin')) {
            return redirect()->route('dashboard.index');
        }

        // Dosen diarahkan ke halaman dokumen perkuliahan berdasarkan NIDN
        if ($user->hasRole('dosen')) {
            $lecturer = Lecturer::where('user_id', $user->id)->first();
            if ($lecturer) {
                return redirect()->route('d.dokumen_perkuliahan', ['nidn' => $lecturer->nidn]);
            }
            return redirect()->route('dashboard.index');
        }

        // Mahasiswa diarahkan ke halaman dokumen perkuliahan berdasarkan id student
        if ($user->hasRole('mahasiswa')) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                return redirect()->route('m.dokumen_perkuliahan', ['id' => $student->id]);
            }
            return redirect()->route('dashboard.index');
        }

        // Fallback untuk role lain
        return redirect()->route('dashboard.index');
    }
}
