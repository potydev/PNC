# PNC Monorepo - Integrasi SSO 3 Aplikasi

Dokumen ini menjelaskan arsitektur, lokasi kode, alur pengembangan, dan lokasi penyimpanan data login untuk integrasi SSO pada 3 aplikasi:

- `jkb-sistem-dosen-wali`
- `jkb-sistem-perkuliahan`
- `ta-sipta-mariaine`

---

## 1. Ringkasan Arsitektur

Implementasi yang dipakai adalah **Central SSO Service** menggunakan Go, sedangkan 3 aplikasi Laravel bertindak sebagai **SSO Client**.

### Komponen utama

1. **SSO Service (Go)**
	 - Lokasi: `sso-golang/`
	 - Fungsi: autentikasi terpusat, issue authorization code, issue access token, userinfo, login Google.

2. **Aplikasi Client Laravel**
	 - Lokasi:
		 - `jkb-sistem-dosen-wali/`
		 - `jkb-sistem-perkuliahan/`
		 - `ta-sipta-mariaine/`
	 - Fungsi: redirect ke SSO, callback code, tukar token, ambil profil, lalu login user lokal.

### Pola alur (high level)

1. User klik **Login dengan SSO** di aplikasi Laravel.
2. Laravel redirect ke SSO endpoint `/authorize`.
3. SSO minta user login (email/password SSO atau Google).
4. SSO redirect kembali ke callback Laravel dengan `code`.
5. Laravel call `/token` dan `/userinfo` ke SSO.
6. Laravel sinkronkan user lokal (`users`) dan login session aplikasi.

---

## 2. Struktur Folder dan Lokasi Kode Penting

### Root

- `Readme.md` -> dokumen ini.

### SSO Service (Go)

- `sso-golang/cmd/sso/main.go`
	- Entry point service SSO.
	- Daftar endpoint (`/authorize`, `/login`, `/login/google`, `/token`, `/userinfo`, `/logout`, `/health`).
	- Logic OAuth-style flow, validasi client, cookie session, JWT.

- `sso-golang/sql/schema.sql`
	- Definisi tabel SSO:
		- `sso_users`
		- `sso_clients`
		- `sso_auth_codes`

- `sso-golang/.env.example`
	- Template environment variable SSO (aman untuk commit).

- `sso-golang/README.md`
	- Panduan spesifik service SSO.

- `sso-golang/deploy.md`
	- Panduan deploy (termasuk catatan VPS/Dokploy).

- `sso-golang/Dockerfile` dan `sso-golang/dokploy-compose.yml`
	- Artefak containerisasi/deploy.

### Integrasi Client Laravel

### `jkb-sistem-dosen-wali`

- `jkb-sistem-dosen-wali/app/Http/Controllers/Auth/SsoAuthController.php`
	- Redirect ke SSO dan callback penukaran token.
- `jkb-sistem-dosen-wali/config/sso.php`
	- Konfigurasi base URL SSO, client id/secret, redirect uri.
- `jkb-sistem-dosen-wali/routes/auth.php`
	- Route SSO redirect + callback.
- `jkb-sistem-dosen-wali/resources/views/auth/login.blade.php`
	- Tombol login SSO.

### `jkb-sistem-perkuliahan`

- `jkb-sistem-perkuliahan/app/Http/Controllers/Auth/SsoAuthController.php`
- `jkb-sistem-perkuliahan/config/sso.php`
- `jkb-sistem-perkuliahan/routes/auth.php`
- `jkb-sistem-perkuliahan/resources/views/auth/login.blade.php`

### `ta-sipta-mariaine`

- `ta-sipta-mariaine/app/Http/Controllers/Auth/SsoAuthController.php`
- `ta-sipta-mariaine/config/sso.php`
- `ta-sipta-mariaine/routes/auth.php`
- `ta-sipta-mariaine/resources/views/auth/login.blade.php`

---

## 3. Data SSO Login Disimpan di Mana?

Pertanyaan inti: **data login SSO tersimpan di database `sso_central`** (bukan di satu tabel global milik ketiga Laravel app).

### Database SSO pusat

- DB: `sso_central`
- Tabel:

1. `sso_users`
	 - Menyimpan akun pusat SSO
	 - Kolom penting:
		 - `id`
		 - `name`
		 - `email`
		 - `password_hash`
		 - `role`
	 - Untuk login Google, user bisa dibuat otomatis (auto-provision) jika belum ada.

2. `sso_clients`
	 - Menyimpan daftar aplikasi yang boleh pakai SSO
	 - Kolom penting:
		 - `client_id`
		 - `client_secret`
		 - `redirect_uris`

3. `sso_auth_codes`
	 - Menyimpan authorization code sementara sebelum ditukar token.

### Database aplikasi masing-masing (tetap terpisah)

- `jkb-sistem-dosen-wali` -> DB `si_perwalian`
- `jkb-sistem-perkuliahan` -> DB `siperkuliahan`
- `ta-sipta-mariaine` -> currently SQLite lokal (`database/database.sqlite`) kecuali dipindah ke MySQL.

Di DB aplikasi, tabel `users` tetap dipakai untuk sesi dan otorisasi lokal aplikasi.
Artinya:

- **Autentikasi pusat**: di `sso_central`.
- **Data domain aplikasi** (mahasiswa, dosen, perkuliahan, TA): tetap di DB aplikasi masing-masing.

---

## 4. Upgrade yang Dilakukan (Before -> After)

### Sebelum upgrade

- Tiap aplikasi login lokal email/password sendiri.
- Tidak ada pusat autentikasi lintas aplikasi.

### Sesudah upgrade tahap 1 (Central SSO)

- Ditambahkan SSO terpusat (Go).
- Semua aplikasi login via flow:
	- Redirect `/authorize`
	- Callback `code`
	- Exchange `/token`
	- Ambil profil `/userinfo`

### Sesudah upgrade tahap 2 (Google Domain Restriction)

- Ditambahkan endpoint:
	- `/login/google`
	- `/login/google/callback`
- Login hanya menerima domain kampus (`pnc.ac.id`).
- Validasi dilakukan di SSO service (server-side), bukan hanya UI.

---

## 5. Environment Variable Penting

### SSO (`sso-golang/.env`)

- `SSO_ADDR`
- `SSO_DB_DSN`
- `SSO_JWT_SECRET`
- `SSO_SESSION_COOKIE`
- `SSO_OAUTH_FLOW_COOKIE`
- `SSO_CODE_TTL_MINUTES`
- `SSO_TOKEN_TTL_MINUTES`
- `SSO_ADMIN_EMAIL`
- `SSO_ADMIN_PASSWORD`
- `GOOGLE_OAUTH_CLIENT_ID`
- `GOOGLE_OAUTH_CLIENT_SECRET`
- `GOOGLE_OAUTH_REDIRECT_URL`
- `GOOGLE_OAUTH_HOSTED_DOMAIN`

### Tiap Laravel app (`.env` masing-masing)

- `SSO_BASE_URL`
- `SSO_CLIENT_ID`
- `SSO_CLIENT_SECRET`
- `SSO_REDIRECT_URI`
- `SSO_AFTER_LOGIN_ROUTE`

Catatan keamanan:

- Jangan commit `sso-golang/.env` (berisi secret nyata).
- Gunakan `sso-golang/.env.example` untuk template.

---

## 6. Endpoint SSO yang Digunakan

- `GET /health` -> cek service hidup
- `GET /authorize` -> minta authorization code
- `GET /login` -> halaman login SSO
- `POST /login` -> login email/password SSO
- `GET /login/google` -> redirect ke Google OAuth
- `GET /login/google/callback` -> callback dari Google
- `POST /token` -> tukar code jadi access token
- `GET /userinfo` -> ambil profil user dari token
- `GET /logout` -> logout SSO

---

## 7. Menjalankan untuk Development (Lokal)

Pastikan DB SSO sudah ada dan `sso_clients` sudah terisi.

```bash
# Terminal 1: SSO
cd /home/potydev/PNC/sso-golang
go run cmd/sso/main.go

# Terminal 2: Dosen Wali
cd /home/potydev/PNC/jkb-sistem-dosen-wali
php artisan serve --port=8000

# Terminal 3: Perkuliahan
cd /home/potydev/PNC/jkb-sistem-perkuliahan
php artisan serve --port=8001

# Terminal 4: SIPTA
cd /home/potydev/PNC/ta-sipta-mariaine
php artisan serve --port=8002
```

Jika halaman login Laravel menampilkan error Vite manifest, build asset frontend:

```bash
cd /home/potydev/PNC/jkb-sistem-dosen-wali && npm install && npm run build
cd /home/potydev/PNC/jkb-sistem-perkuliahan && npm install && npm run build
cd /home/potydev/PNC/ta-sipta-mariaine && npm install && npm run build
```

---

## 8. Catatan Role dan Data Lokal User

User baru dari Google dapat tercatat di SSO, tetapi aplikasi domain tetap butuh data lokal.

Contoh pada `jkb-sistem-dosen-wali`:

- User tanpa role/data mahasiswa/dosen bisa gagal di fitur tertentu.
- Sudah ditambahkan guard agar tidak crash saat role belum ada.
- Best practice: setelah first login, admin assign role + lengkapi data domain.

---

## 9. Checklist Cepat Untuk Mentor / Reviewer

- SSO service hidup (`/health` = `{"status":"ok"}`)
- 3 aplikasi bisa redirect ke SSO
- Google login hanya domain `@pnc.ac.id`
- Callback kembali ke aplikasi dan membuat/login user lokal
- User tanpa role ditangani dengan aman (tidak crash)
- Secret sensitif tidak di-commit (`.env` real, binary build)

---

## 10. Referensi File (Cepat)

- SSO entrypoint: `sso-golang/cmd/sso/main.go`
- SSO schema: `sso-golang/sql/schema.sql`
- SSO env template: `sso-golang/.env.example`
- Deploy guide: `sso-golang/deploy.md`

- Dosen Wali SSO controller: `jkb-sistem-dosen-wali/app/Http/Controllers/Auth/SsoAuthController.php`
- Perkuliahan SSO controller: `jkb-sistem-perkuliahan/app/Http/Controllers/Auth/SsoAuthController.php`
- SIPTA SSO controller: `ta-sipta-mariaine/app/Http/Controllers/Auth/SsoAuthController.php`

Dokumen ini bisa dipakai sebagai baseline onboarding developer baru dan bahan presentasi arsitektur ke mentor.







public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        $query = http_build_query([
            'client_id'    => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'state'        => $state,
        ]);

        return redirect()->away(rtrim(config('sso.base_url'), '/').'/authorize?'.$query);
    }

 baris ssoauthcontroller 24-25

login/health

login ketika tidak ada client id : error
silahkan login halaman awal


tx database


rotasi =

	_, err = tx.ExecContext(r.Context(), "UPDATE sso_auth_codes SET used_at = ? WHERE code = ?", time.Now(), code)
	if err != nil {
		http.Error(w, "unable to consume code", http.StatusInternalServerError)
		return
	}



membongkar jwt : rs25 