<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'state' => $state,
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

        $tokenResponse = Http::asForm()
            ->timeout(10)
            ->post(rtrim(config('sso.base_url'), '/').'/token', [
                'grant_type' => 'authorization_code',
                'code' => $request->string('code')->toString(),
                'client_id' => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
                'redirect_uri' => config('sso.redirect_uri'),
            ]);

        if ($tokenResponse->failed()) {
            return redirect()->route('login')->withErrors(['sso' => 'Gagal menukar code SSO ke token.']);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (! is_string($accessToken) || $accessToken === '') {
            return redirect()->route('login')->withErrors(['sso' => 'Token SSO tidak valid.']);
        }

        $userInfoResponse = Http::withToken($accessToken)
            ->timeout(10)
            ->get(rtrim(config('sso.base_url'), '/').'/userinfo');

        if ($userInfoResponse->failed()) {
            return redirect()->route('login')->withErrors(['sso' => 'Gagal mengambil profil user SSO.']);
        }

        $profile = $userInfoResponse->json();
        $email = data_get($profile, 'email');

        if (! is_string($email) || $email === '') {
            return redirect()->route('login')->withErrors(['sso' => 'Email user SSO tidak ditemukan.']);
        }

        $name = data_get($profile, 'name', Str::before($email, '@'));
        $roleName = data_get($profile, 'role');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => (string) $name,
                'password' => Hash::make(Str::random(40)),
            ]
        );

        if (is_string($roleName) && $roleName !== '' && method_exists($user, 'assignRole')) {
            $roleExists = Role::where('name', $roleName)->exists();
            if ($roleExists && $user->roles()->count() === 0) {
                $user->assignRole($roleName);
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route(config('sso.after_login_route'), absolute: false));
    }
}
