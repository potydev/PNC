<?php

// Konfigurasi integrasi SSO untuk aplikasi perkuliahan.
return [
    // URL base service SSO pusat.
    'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8088'),

    // Kredensial OAuth client milik aplikasi ini.
    'client_id' => env('SSO_CLIENT_ID', 'perkuliahan-app'),
    'client_secret' => env('SSO_CLIENT_SECRET', 'change-me-perkuliahan'),

    // Callback endpoint setelah user login sukses di SSO.
    'redirect_uri' => env('SSO_REDIRECT_URI', rtrim(env('APP_URL', 'http://localhost'), '/').'/auth/sso/callback'),

    // Route default setelah login sukses (dipakai sebagai fallback).
    'after_login_route' => env('SSO_AFTER_LOGIN_ROUTE', 'dashboard.index'),
];
