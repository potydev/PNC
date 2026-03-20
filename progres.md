20 - 03 - 2026 : menambahkan komen ke semua code yang belum ada

21 - 03 - 2026 : memisahkan/refactor beberapa code menjadi beberapa file di main.go supaya gamapang ketika ingin debug. templates/login.html , handlers_auth.go , handlers_google.go , handlers_oauth.go

md/sso/templates/login.html

Peran: tampilan UI login SSO.
Isi: form email/password + tombol login Google + hidden field (client_id, redirect_uri, state).
Intinya: hanya presentasi halaman, bukan logic backend.
cmd/sso/templates.go

Peran: jembatan antara backend dan file HTML.
Isi: go:embed untuk menyertakan templates/login.html ke binary, plus fungsi renderLogin(...).
Intinya: merender halaman login dengan data dinamis (nama app, state, error, allowed domain).
cmd/sso/handlers_auth.go

Peran: alur auth inti non-Google.
Endpoint/fungsi: authorize, login, loginPost.
Tugas:
validasi client + redirect URI
cek session user
jika perlu tampilkan login form
verifikasi email/password
buat auth code dan redirect balik ke aplikasi client
Intinya: flow OAuth Authorization Code standar (bagian login lokal SSO).
cmd/sso/handlers_google.go

Peran: alur login via Google.
Endpoint/fungsi: loginGoogle, loginGoogleCallback.
Tugas:
redirect user ke Google OAuth
validasi callback/state
ambil profil Google
batasi domain (pnc.ac.id)
sinkron user ke sso_users
lanjutkan ke /authorize bila dipicu dari app client
Intinya: ekstensi autentikasi Google di atas flow SSO utama.
cmd/sso/handlers_oauth.go

Peran: endpoint token & resource OAuth.
Endpoint/fungsi: token, userinfo, logout.
Tugas:
tukar authorization code jadi access token (/token)
kirim profil user dari bearer token (/userinfo)
hapus session cookie (/logout)
Intinya: “mesin” pertukaran token dan profil user untuk aplikasi client.