# TRPL SHOWCASE

Portal terpusat untuk menampilkan semua aplikasi Teknologi Rekayasa Perangkat Lunak (TRPL) dengan sistem Single Sign-On (SSO) terintegrasi.

## 📋 Deskripsi

**TRPL Showcase** adalah aplikasi portal yang mengagregasikan dan menampilkan semua aplikasi akademik TRPL dalam satu dashboard terpusat. Aplikasi ini menggunakan sistem SSO pusat untuk autentikasi, sehingga pengguna hanya perlu login sekali untuk mengakses semua aplikasi yang tersedia.

## 🎯 Fitur Utama

- ✅ **Mandatory SSO Login** - Semua pengguna harus login melalui SSO sebelum mengakses dashboard
- ✅ **OAuth 2.0 Authorization Code Flow** - Integrasi aman dengan server SSO pusat
- ✅ **Dashboard Showcase** - Menampilkan daftar aplikasi TRPL dengan informasi dan link
- ✅ **Global Logout** - Logout dari showcase sekaligus invalidate SSO session
- ✅ **User Profile Management** - Otomatis membuat/update profil pengguna dari SSO
- ✅ **JWT Authentication** - Menggunakan JWT token dari SSO untuk session management
- ✅ **Responsive Design** - Interface yang responsif dan modern (dark theme)

## 📱 Aplikasi yang Ditampilkan

1. **JKB Sistem Dosen Wali** (Port 8000)
   - Monitoring perwalian, validasi, dan manajemen dosen wali

2. **JKB Sistem Perkuliahan** (Port 8001)
   - Dokumen perkuliahan, jurnal, absensi, dan persetujuan

3. **TA SIPTA Mariaine** (Port 8002)
   - Aplikasi tugas akhir dan administrasi akademik terkait

## 🚀 Instalasi & Setup

### Prasyarat
- PHP 8.3+
- Composer
- Laravel 13.1+
- Database SQLite atau MySQL
- Server SSO pusat berjalan (port 8088)

### Langkah Instalasi

1. **Clone atau navigate ke project folder**
   ```bash
   cd /home/potydev/PNC/trpl-showcase
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Setup database**
   ```bash
   php artisan migrate
   ```

6. **Konfigurasi SSO** (update `.env`)
   ```env
   SSO_BASE_URL=http://127.0.0.1:8088
   SSO_CLIENT_ID=showcase-app
   SSO_CLIENT_SECRET=secret-showcase-2026
   SSO_REDIRECT_URI=http://127.0.0.1:8003/auth/sso/callback
   APP_URL=http://127.0.0.1:8003
   ```

7. **Jalankan development server**
   ```bash
   php artisan serve --port=8003
   ```

## 📚 Arsitektur & Flow

### OAuth 2.0 Authorization Code Flow

```
User → Showcase (/) 
   → [Unauthenticated] → Redirect ke SSO /authorize
   → SSO Login Page → User enter credentials
   → SSO generate auth code → Redirect ke /auth/sso/callback
   → Showcase exchange code → Get JWT token dari SSO /token
   → Showcase fetch userinfo → Get user profile dari SSO /userinfo
   → Create/update user di DB → Login user → Redirect ke /showcase
   → [Dashboard with app cards] → User can click app links
```

### Logout Flow

```
User click Logout
   → Showcase invalidate session
   → Redirect ke SSO /logout
   → SSO invalidate SSO session
   → Redirect back ke Showcase login
```

## 📁 Struktur Project

```
trpl-showcase/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       │   └── SsoAuthController.php      # OAuth flow handler
│   │       └── ShowcaseController.php         # Dashboard showcase
│   └── Models/
│       └── User.php
├── config/
│   └── sso.php                               # SSO configuration
├── routes/
│   └── web.php                               # Route definitions
├── resources/
│   └── views/
│       └── showcase/
│           └── index.blade.php               # Dashboard view
├── database/
│   ├── migrations/
│   └── seeders/
├── .env                                      # Environment config
└── README.md                                 # Dokumentasi ini
```

## 🔐 Konfigurasi SSO

### Environment Variables (.env)

| Variabel | Deskripsi | Default |
|----------|-----------|---------|
| `SSO_BASE_URL` | Base URL server SSO pusat | `http://127.0.0.1:8088` |
| `SSO_CLIENT_ID` | Client identifier di SSO | `showcase-app` |
| `SSO_CLIENT_SECRET` | Client secret untuk secure communication | `secret-showcase-2026` |
| `SSO_REDIRECT_URI` | OAuth callback URL (harus match di SSO database) | `http://127.0.0.1:8003/auth/sso/callback` |
| `APP_URL` | Base URL aplikasi showcase | `http://127.0.0.1:8003` |

### Database Registration (SSO Central)

Client showcase harus didaftarkan di table `sso_clients` pada database SSO:

```sql
INSERT INTO sso_clients (client_id, client_secret, name, redirect_uris) 
VALUES (
    'showcase-app',
    'secret-showcase-2026',
    'TRPL SHOWCASE',
    'http://127.0.0.1:8003/auth/sso/callback'
);
```

## 🛣️ Route Definitions

| Method | Route | Handler | Auth | Deskripsi |
|--------|-------|---------|------|-----------|
| GET | `/` | - | ❌ | Entry point - redirect ke showcase atau SSO |
| GET | `/auth/sso/redirect` | `SsoAuthController@redirect` | ❌ | Initiate OAuth flow |
| GET | `/auth/sso/callback` | `SsoAuthController@callback` | ❌ | Handle OAuth callback |
| GET | `/showcase` | `ShowcaseController@index` | ✅ | Dashboard showcase (protected) |
| POST | `/logout` | `SsoAuthController@logout` | ✅ | Global logout (protected) |

## 🔧 File Konfigurasi Utama

### `config/sso.php`
Konfigurasi OAuth dan SSO integration:
- Base URL server SSO
- Client credentials (ID, Secret)
- Redirect URI untuk OAuth callback
- Fallback ke environment variables

### `app/Http/Controllers/Auth/SsoAuthController.php`
Handler untuk OAuth flow:
- `redirect()` - Inisiasi OAuth dengan state parameter
- `callback()` - Handle OAuth callback, exchange code untuk JWT
- `logout()` - Logout global ke SSO

### `app/Http/Controllers/ShowcaseController.php`
Handler untuk showcase dashboard:
- `index()` - Menampilkan list aplikasi TRPL

### `routes/web.php`
Definisi route dengan dokumentasi:
- Mandatory auth check di root
- OAuth redirect dan callback
- Protected routes dengan middleware auth

## 🔄 Flow Diagrams

### Login Flow
```
┌─────────────────────────────────────────────────────┐
│ 1. User access http://127.0.0.1:8003               │
└──────────────────┬──────────────────────────────────┘
                   │
                   ├─[NOT AUTH]─→ Redirect /auth/sso/redirect
                   │
                   └─[AUTH]─→ Redirect /showcase (Dashboard)

┌─────────────────────────────────────────────────────┐
│ 2. /auth/sso/redirect - Generate state & redirect  │
└──────────────────┬──────────────────────────────────┘
                   │
         Generate state parameter
         Save state in session
                   │
                   └──→ Redirect to SSO /authorize?client_id=...&state=...

┌─────────────────────────────────────────────────────┐
│ 3. User login di SSO (email/password atau Google)  │
└──────────────────┬──────────────────────────────────┘
                   │
         SSO validate credentials
         Generate auth code
                   │
                   └──→ Redirect /auth/sso/callback?code=...&state=...

┌─────────────────────────────────────────────────────┐
│ 4. /auth/sso/callback - Exchange code for token    │
└──────────────────┬──────────────────────────────────┘
                   │
         Validate state vs session
         POST /token ke SSO (code + client_secret)
         Get JWT access_token
                   │
                   ├──→ GET /userinfo (fetch user profile)
                   │
         Create/update user di DB
         Login user ke session
         Regenerate session ID
                   │
                   └──→ Redirect /showcase (Success!)

┌─────────────────────────────────────────────────────┐
│ 5. /showcase - Display dashboard dengan app cards  │
└─────────────────────────────────────────────────────┘
```

## 🧪 Testing

### Test di Browser
```
1. Open http://127.0.0.1:8003
2. Should redirect to SSO login page
3. Enter credentials
4. Should redirect back to showcase dashboard
5. Click app link to verify links work
6. Click logout to test logout flow
```

### Test dengan cURL
```bash
# Test root endpoint
curl -i http://127.0.0.1:8003/

# Test showcase (will 302 redirect without auth)
curl -i http://127.0.0.1:8003/showcase

# Test SSO redirect
curl -i http://127.0.0.1:8003/auth/sso/redirect
```

## 🛠️ Troubleshooting

### Issue: "State SSO tidak cocok"
**Penyebab**: Session state tidak match saat callback
**Solusi**: Clear browser cookies dan coba login ulang

### Issue: "Gagal menukar code SSO ke token"
**Penyebab**: SSO server tidak berjalan atau client_secret salah
**Solusi**: Verifikasi SSO running di port 8088 dan check config di `.env`

### Issue: "Redirect URI tidak match"
**Penyebab**: `SSO_REDIRECT_URI` di `.env` tidak match di database SSO
**Solusi**: Update `sso_clients.redirect_uris` atau `SSO_REDIRECT_URI` di `.env`

### Issue: "User profile tidak ditemukan"
**Penyebab**: SSO /userinfo endpoint gagal atau JWT token invalid
**Solusi**: Verifikasi JWT token valid dan SSO userinfo endpoint accessible

## 📊 Dependencies

- **Framework**: Laravel 13.1
- **PHP**: 8.3+
- **Database**: SQLite (default) atau MySQL
- **HTTP Client**: Guzzle (via Illuminate\Http)
- **Authentication**: JWT via SSO + Laravel Sessions

## 📝 Notes

- Aplikasi menggunakan JWT token dari SSO untuk authentication
- Local session disimpan di database (database driver)
- Setiap login akan auto-create user di database showcase jika belum ada
- Logout invalidate both local session dan SSO session
- State parameter diperlukan untuk CSRF protection pada OAuth flow

## 🤝 Hubungi Developer

Untuk pertanyaan atau issue, silakan hubungi tim development TRPL.

---

**Last Updated**: March 2026  
**Version**: 1.0  
**License**: Proprietary - TRPL

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
