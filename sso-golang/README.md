# SSO Golang (Central Auth)

SSO terpusat untuk 3 aplikasi Laravel di workspace ini:

- `jkb-sistem-dosen-wali`
- `jkb-sistem-perkuliahan`
- `ta-sipta-mariaine`

Arsitektur:

- **1 database SSO** (MySQL) untuk user dan OAuth-like auth code flow.
- 3 aplikasi Laravel bertindak sebagai client SSO.

Kebutuhan minimum:

- Go `1.25+`

## 1) Jalankan SSO Service

```bash
cd /home/potydev/PNC/sso-golang
cp .env.example .env
```

Isi env di shell (atau export di service manager):

```bash
export SSO_ADDR=:8088
export SSO_DB_DSN='root:password@tcp(127.0.0.1:3306)/sso_central?parseTime=true'
export SSO_JWT_SECRET='replace-with-long-random-secret'
export SSO_SESSION_COOKIE='sso_session'
export SSO_CODE_TTL_MINUTES=5
export SSO_TOKEN_TTL_MINUTES=60
export SSO_ADMIN_EMAIL='admin@sso.local'
export SSO_ADMIN_PASSWORD='Admin#2026!'
export SSO_OAUTH_FLOW_COOKIE='sso_oauth_flow'
export GOOGLE_OAUTH_CLIENT_ID='isi-client-id-google'
export GOOGLE_OAUTH_CLIENT_SECRET='isi-client-secret-google'
export GOOGLE_OAUTH_REDIRECT_URL='http://127.0.0.1:8088/login/google/callback'
export GOOGLE_OAUTH_HOSTED_DOMAIN='pnc.ac.id'
```

Jalankan:

```bash
go mod tidy
go run ./cmd/sso
```

Health check:

```bash
curl http://127.0.0.1:8088/health
```

## 2) Siapkan Database SSO

Service akan auto-create tabel saat startup. Anda juga bisa jalankan SQL manual di `sql/schema.sql`.

Tambahkan daftar client aplikasi (wajib):

```sql
INSERT INTO sso_clients (client_id, client_secret, name, redirect_uris)
VALUES
('dosen-wali-app', 'change-me-dosen-wali', 'JKB Dosen Wali', 'http://127.0.0.1:8000/auth/sso/callback'),
('perkuliahan-app', 'change-me-perkuliahan', 'JKB Perkuliahan', 'http://127.0.0.1:8001/auth/sso/callback'),
('sipta-app', 'change-me-sipta', 'SIPTA', 'http://127.0.0.1:8002/auth/sso/callback')
ON DUPLICATE KEY UPDATE
client_secret = VALUES(client_secret),
name = VALUES(name),
redirect_uris = VALUES(redirect_uris);
```

Tambahkan user SSO awal (contoh):

```sql
-- password hash bcrypt untuk "12345678" (contoh)
INSERT INTO sso_users (name, email, password_hash, role)
VALUES ('Admin SSO', 'admin@gmail.com', '$2y$10$2B3x4m6va4Sg9fdi4G6ROu8lH81LT6Qv8z5rjHGmQqbo1q6j5RzHC', 'admin')
ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role);
```

> Disarankan generate hash bcrypt sendiri untuk password produksi.

## 3) Konfigurasi 3 Laravel App

Semua aplikasi sudah ditambahkan:

- route: `/auth/sso/redirect`, `/auth/sso/callback`
- tombol **Login dengan SSO** di halaman login
- controller callback SSO
- config file `config/sso.php`

Isi env masing-masing app:

### `jkb-sistem-dosen-wali`

```dotenv
SSO_BASE_URL=http://127.0.0.1:8088
SSO_CLIENT_ID=dosen-wali-app
SSO_CLIENT_SECRET=change-me-dosen-wali
SSO_REDIRECT_URI=${APP_URL}/auth/sso/callback
SSO_AFTER_LOGIN_ROUTE=dashboard
```

### `jkb-sistem-perkuliahan`

```dotenv
SSO_BASE_URL=http://127.0.0.1:8088
SSO_CLIENT_ID=perkuliahan-app
SSO_CLIENT_SECRET=change-me-perkuliahan
SSO_REDIRECT_URI=${APP_URL}/auth/sso/callback
SSO_AFTER_LOGIN_ROUTE=dashboard.index
```

### `ta-sipta-mariaine`

```dotenv
SSO_BASE_URL=http://127.0.0.1:8088
SSO_CLIENT_ID=sipta-app
SSO_CLIENT_SECRET=change-me-sipta
SSO_REDIRECT_URI=${APP_URL}/auth/sso/callback
SSO_AFTER_LOGIN_ROUTE=dashboard
```

Clear config setelah update env:

```bash
php artisan config:clear
```

## 4) Menjalankan Semua Aplikasi (contoh port)

```bash
# terminal 1
cd /home/potydev/PNC/sso-golang && go run ./cmd/sso

# terminal 2
cd /home/potydev/PNC/jkb-sistem-dosen-wali && php artisan serve --port=8000

# terminal 3
cd /home/potydev/PNC/jkb-sistem-perkuliahan && php artisan serve --port=8001

# terminal 4
cd /home/potydev/PNC/ta-sipta-mariaine && php artisan serve --port=8002
```

Lalu akses login salah satu app dan klik **Login dengan SSO**.

## Endpoint SSO

- `GET /authorize`
- `GET /login`
- `GET /login/google`
- `GET /login/google/callback`
- `POST /login`
- `POST /token`
- `GET /userinfo`
- `GET /logout`
- `GET /health`

## Deploy ke Dokploy (VPS)

- Detail langkah deploy production ada di `deploy.md`.
- Artefak deploy yang disiapkan:
	- `Dockerfile`
	- `.dockerignore`
	- `dokploy-compose.yml`

Poin penting production:

- Gunakan domain HTTPS untuk SSO, contoh `https://sso.domainkamu.com`.
- Set `GOOGLE_OAUTH_REDIRECT_URL=https://sso.domainkamu.com/login/google/callback`.
- Set `SSO_BASE_URL` pada semua Laravel app ke domain HTTPS SSO tersebut.

## Catatan

- Flow yang dipakai: Authorization Code (sederhana).
- Untuk produksi, aktifkan HTTPS, rotate secret, rate limit, CSRF/session hardening, audit log, dan revocation token.
