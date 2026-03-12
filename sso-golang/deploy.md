# Deploy SSO Golang ke VPS Dokploy

## 1) Google OAuth setup (WAJIB)

1. Buka Google Cloud Console.
2. Buat project atau pilih project existing.
3. Enable API: **Google Identity Services / OAuth consent screen**.
4. Buat **OAuth 2.0 Client ID** (Web application).
5. Isi **Authorized redirect URI**:
   - `https://sso.domainkamu.com/login/google/callback`
6. Simpan `Client ID` dan `Client Secret`.

> Domain restriction dilakukan server-side di SSO (`GOOGLE_OAUTH_HOSTED_DOMAIN=pnc.ac.id`).

## 2) Siapkan env di Dokploy

Isi variabel berikut pada service SSO:

```dotenv
SSO_ADDR=:8088
SSO_DB_DSN=sso_user:SsoCentral2026A!@tcp(mysql-host:3306)/sso_central?parseTime=true
SSO_JWT_SECRET=isi-random-secret-panjang-min-32-char
SSO_SESSION_COOKIE=sso_session
SSO_OAUTH_FLOW_COOKIE=sso_oauth_flow
SSO_CODE_TTL_MINUTES=5
SSO_TOKEN_TTL_MINUTES=60
SSO_ADMIN_EMAIL=admin@pnc.ac.id
SSO_ADMIN_PASSWORD=AdminSSO2026!
GOOGLE_OAUTH_CLIENT_ID=isi-dari-google
GOOGLE_OAUTH_CLIENT_SECRET=isi-dari-google
GOOGLE_OAUTH_REDIRECT_URL=https://sso.domainkamu.com/login/google/callback
GOOGLE_OAUTH_HOSTED_DOMAIN=pnc.ac.id
```

## 3) Deploy di Dokploy

### Opsi A - Dockerfile Project
- Source: repo/folder `sso-golang`
- Build: `Dockerfile`
- Expose port: `8088`
- Tambahkan environment variables seperti di atas.
- Attach domain: `sso.domainkamu.com`
- Enable TLS/HTTPS (Let's Encrypt di Dokploy/Traefik).

### Opsi B - Docker Compose
- Pakai file `dokploy-compose.yml`
- Pastikan `.env` berisi variabel valid pada server.

## 4) Reverse proxy + security

- Wajib HTTPS di production.
- Pastikan callback Google menggunakan **https**.
- Tambahkan trusted proxy jika lewat Traefik/Nginx.
- Simpan `SSO_JWT_SECRET` di secret manager Dokploy.

## 5) Daftarkan client aplikasi Laravel

Di database `sso_central`, insert/update client:

```sql
INSERT INTO sso_clients (client_id, client_secret, name, redirect_uris)
VALUES
('dosen-wali-app', 'change-me-dosen-wali', 'JKB Dosen Wali', 'https://dosenwali.domainkamu.com/auth/sso/callback'),
('perkuliahan-app', 'change-me-perkuliahan', 'JKB Perkuliahan', 'https://perkuliahan.domainkamu.com/auth/sso/callback'),
('sipta-app', 'change-me-sipta', 'SIPTA', 'https://sipta.domainkamu.com/auth/sso/callback')
ON DUPLICATE KEY UPDATE
client_secret = VALUES(client_secret),
name = VALUES(name),
redirect_uris = VALUES(redirect_uris);
```

## 6) Konfigurasi tiap Laravel app

Set env di masing-masing app:

```dotenv
SSO_BASE_URL=https://sso.domainkamu.com
SSO_CLIENT_ID=...
SSO_CLIENT_SECRET=...
SSO_REDIRECT_URI=https://app.domainkamu.com/auth/sso/callback
```

Lalu:

```bash
php artisan config:clear
```

## 7) Smoke test

1. Akses login salah satu app Laravel.
2. Klik `Login dengan SSO`.
3. Login Google akun `@pnc.ac.id`.
4. Harus kembali ke app target dan terautentikasi.
