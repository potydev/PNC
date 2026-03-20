<?php

use App\Http\Controllers\Auth\SsoAuthController;
use App\Http\Controllers\ShowcaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('showcase.index');
    }

    return redirect()->route('sso.redirect');
});

Route::get('/auth/sso/redirect', [SsoAuthController::class, 'redirect'])
    ->name('sso.redirect');

Route::get('/auth/sso/callback', [SsoAuthController::class, 'callback'])
    ->name('sso.callback');

Route::middleware('auth')->group(function () {
    Route::get('/showcase', [ShowcaseController::class, 'index'])
        ->name('showcase.index');

    Route::post('/logout', [SsoAuthController::class, 'logout'])
        ->name('logout');
});
