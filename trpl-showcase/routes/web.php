<?php

use App\Http\Controllers\Auth\SsoAuthController;
use App\Http\Controllers\ShowcaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Web Routes - TRPL Showcase Application
 * 
 * Route structure:
 * - Root (/) - Entry point with mandatory authentication check
 * - /auth/sso/* - OAuth flow endpoints (redirect, callback)
 * - /showcase - Authenticated showcase dashboard
 * - /logout - Global SSO logout endpoint
 * 
 * All routes except OAuth endpoints require authentication.
 * Unauthenticated requests redirect to SSO login.
 */

/**
 * Root Route - Entry Point
 * 
 * Mandatory authentication enforcement:
 * - Authenticated users → showcase dashboard
 * - Unauthenticated users → SSO login redirect
 */
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('showcase.index');
    }

    return redirect()->route('sso.redirect');
});

/**
 * SSO OAuth Authorization Redirect
 * 
 * Initiates OAuth 2.0 Authorization Code Flow with central SSO service.
 * Generates state parameter and redirects to /authorize endpoint.
 */
Route::get('/auth/sso/redirect', [SsoAuthController::class, 'redirect'])
    ->name('sso.redirect');

/**
 * SSO OAuth Callback Handler
 * 
 * Receives authorization code and state from SSO service.
 * Exchanges code for JWT token and authenticates user.
 */
Route::get('/auth/sso/callback', [SsoAuthController::class, 'callback'])
    ->name('sso.callback');

/**
 * Protected Routes - Require Authentication
 * 
 * All routes within this group require valid user session.
 * Unauthenticated requests are redirected by auth middleware.
 */
Route::middleware('auth')->group(function () {
    /**
     * Showcase Dashboard - Display TRPL Applications
     * 
     * Shows portfolio of available applications with links.
     * Requires authentication.
     */
    Route::get('/showcase', [ShowcaseController::class, 'index'])
        ->name('showcase.index');

    /**
     * Global Logout - Clear Session and SSO Session
     * 
     * Logs out user locally and invalidates SSO session.
     * Requires authentication.
     */
    Route::post('/logout', [SsoAuthController::class, 'logout'])
        ->name('logout');
});
