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

/**
 * SsoAuthController
 * 
 * Handles OAuth 2.0 Authorization Code Flow integration with central SSO service.
 * Manages OAuth flow redirects, token exchange, user profile fetching, and logout.
 * 
 * SSO Flow:
 * 1. redirect() - Initiates OAuth flow, generates state parameter
 * 2. callback() - Handles OAuth callback, exchanges code for JWT token
 * 3. logout() - Clears session and invalidates SSO session
 * 
 * @category Controller
 * @package App\Http\Controllers\Auth
 */
class SsoAuthController extends Controller
{
    /**
     * Initiate SSO OAuth Authorization Code Flow
     * 
     * Generates a state parameter to prevent CSRF attacks and redirects user
     * to SSO service authorization endpoint. State is stored in session for
     * validation during callback.
     * 
     * @param Request $request HTTP request object with session
     * @return RedirectResponse Redirect to SSO authorize endpoint
     */
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

    /**
     * Handle SSO OAuth Callback
     * 
     * Processes OAuth callback from SSO service:
     * - Validates state parameter against CSRF attack
     * - Exchanges authorization code for JWT access token
     * - Fetches user profile from SSO userinfo endpoint
     * - Creates or updates local user record
     * - Establishes authenticated session
     * 
     * @param Request $request HTTP request with code, state, and session
     * @return RedirectResponse Redirect to showcase or error route
     */
    public function callback(Request $request): RedirectResponse
    {
        if (! $request->filled('code') || ! $request->filled('state')) {
            return redirect()->route('sso.redirect')->withErrors(['sso' => 'SSO callback tidak valid.']);
        }

        if ($request->session()->pull('sso_state') !== $request->string('state')->toString()) {
            return redirect()->route('sso.redirect')->withErrors(['sso' => 'State SSO tidak cocok.']);
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
            return redirect()->route('sso.redirect')->withErrors(['sso' => 'Gagal menukar code SSO ke token.']);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (! is_string($accessToken) || $accessToken === '') {
            return redirect()->route('sso.redirect')->withErrors(['sso' => 'Token SSO tidak valid.']);
        }

        $userInfoResponse = Http::withToken($accessToken)
            ->timeout(10)
            ->get(rtrim(config('sso.base_url'), '/').'/userinfo');

        if ($userInfoResponse->failed()) {
            return redirect()->route('sso.redirect')->withErrors(['sso' => 'Gagal mengambil profil user SSO.']);
        }

        $profile = $userInfoResponse->json();
        $email = data_get($profile, 'email');
        if (! is_string($email) || $email === '') {
            return redirect()->route('sso.redirect')->withErrors(['sso' => 'Email user SSO tidak ditemukan.']);
        }

        $name = (string) data_get($profile, 'name', Str::before($email, '@'));

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(40)),
            ]
        );

        if ($user->name !== $name) {
            $user->update(['name' => $name]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('showcase.index');
    }

    /**
     * Global SSO Logout
     * 
     * Performs complete logout procedure:
     * - Invalidates local session
     * - Redirects to SSO logout endpoint to clear SSO session
     * - SSO service redirects back to showcase login
     * 
     * @param Request $request HTTP request with authentication data
     * @return RedirectResponse Redirect to SSO logout endpoint
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirectBack = route('sso.redirect');
        $ssoLogoutUrl = rtrim(config('sso.base_url'), '/').'/logout?redirect='.urlencode($redirectBack);

        return redirect()->away($ssoLogoutUrl);
    }
}
