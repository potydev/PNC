<?php

return [
    'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8088'),
    'client_id' => env('SSO_CLIENT_ID', 'showcase-app'),
    'client_secret' => env('SSO_CLIENT_SECRET', 'secret-showcase-2026'),
    'redirect_uri' => env('SSO_REDIRECT_URI', rtrim(env('APP_URL', 'http://127.0.0.1:8003'), '/').'/auth/sso/callback'),
];
