<?php

/**
 * SSO Configuration
 * 
 * Central Single Sign-On (SSO) service configuration for OAuth 2.0 integration.
 * All values are sourced from environment variables with sensible defaults.
 * 
 * Configuration Keys:
 * - base_url: SSO service base URL (default: http://127.0.0.1:8088)
 * - client_id: OAuth client identifier registered in SSO (default: showcase-app)
 * - client_secret: OAuth client secret for token exchange (default: secret-showcase-2026)
 * - redirect_uri: Callback URL after authorization (default: /auth/sso/callback)
 * 
 * Environment Variables:
 * - SSO_BASE_URL: Base URL of central SSO service
 * - SSO_CLIENT_ID: Client identifier in SSO database
 * - SSO_CLIENT_SECRET: Secret key for secure communication
 * - SSO_REDIRECT_URI: Full callback URL (includes protocol and domain)
 * - APP_URL: Application base URL (used for redirect_uri fallback)
 * 
 * @category Config
 * @package config
 */

return [
    // Central SSO service base URL (e.g., http://127.0.0.1:8088)
    'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8088'),
    
    // OAuth client identifier registered in SSO database sso_clients table
    'client_id' => env('SSO_CLIENT_ID', 'showcase-app'),
    
    // OAuth client secret for secure token exchange during authorization code flow
    'client_secret' => env('SSO_CLIENT_SECRET', 'secret-showcase-2026'),
    
    // OAuth callback URL - must match exactly with redirect_uris in sso_clients table
    // Falls back to APP_URL + /auth/sso/callback if SSO_REDIRECT_URI not set
    'redirect_uri' => env('SSO_REDIRECT_URI', rtrim(env('APP_URL', 'http://127.0.0.1:8003'), '/').'/auth/sso/callback'),
];
