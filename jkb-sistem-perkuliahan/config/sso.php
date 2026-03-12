<?php

return [
    'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8088'),
    'client_id' => env('SSO_CLIENT_ID', 'perkuliahan-app'),
    'client_secret' => env('SSO_CLIENT_SECRET', 'change-me-perkuliahan'),
    'redirect_uri' => env('SSO_REDIRECT_URI', rtrim(env('APP_URL', 'http://localhost'), '/').'/auth/sso/callback'),
    'after_login_route' => env('SSO_AFTER_LOGIN_ROUTE', 'dashboard.index'),
];
