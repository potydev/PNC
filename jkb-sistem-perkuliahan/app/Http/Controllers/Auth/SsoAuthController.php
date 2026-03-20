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

    public function callback(Request $request): RedirectResponse
    {
        if (! $request->filled('code') || ! $request->filled('state')) {
            return redirect()->route('login')->withErrors(['sso' => 'SSO callback tidak valid.']);
        }

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

        // --- Assign role jika belum punya ---
        if (is_string($roleName) && $roleName !== '' && method_exists($user, 'assignRole')) {
            $roleExists = Role::where('name', $roleName)->exists();
            if ($roleExists && $user->roles()->count() === 0) {
                $user->assignRole($roleName);
            }
        }

        // --- Tolak login jika tidak punya role ---
        $user->refresh();
        if ($user->roles()->count() === 0) {
            return redirect()->route('login')->withErrors([
                'sso' => "Akun {$email} belum terdaftar di sistem. Hubungi administrator untuk mendapatkan akses.",
            ]);
        }

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
        if ($user->hasRole('super_admin')) {
            return redirect()->route('dashboard.index');
        }

        if ($user->hasRole('dosen')) {
            $lecturer = Lecturer::where('user_id', $user->id)->first();
            if ($lecturer) {
                return redirect()->route('d.dokumen_perkuliahan', ['nidn' => $lecturer->nidn]);
            }
            return redirect()->route('dashboard.index');
        }

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
