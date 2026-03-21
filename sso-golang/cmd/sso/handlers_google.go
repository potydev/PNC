package main

import (
	"net/http"
	"net/url"
	"strings"
	"time"
)

func (a *app) loginGoogle(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Fitur Google OAuth bersifat opsional; pastikan sudah dikonfigurasi.
	if a.googleOAuthConfig == nil || a.googleOAuthConfig.ClientID == "" || a.googleOAuthConfig.ClientSecret == "" {
		http.Error(w, "google oauth belum dikonfigurasi", http.StatusInternalServerError)
		return
	}

	clientID := strings.TrimSpace(r.URL.Query().Get("client_id"))
	redirectURI := strings.TrimSpace(r.URL.Query().Get("redirect_uri"))
	appState := strings.TrimSpace(r.URL.Query().Get("state"))

	// Jika login Google dipicu dari flow OAuth app tertentu,
	// pastikan pasangan client_id & redirect_uri valid.
	if clientID != "" || redirectURI != "" {
		if clientID == "" || redirectURI == "" {
			http.Error(w, "client_id dan redirect_uri wajib berpasangan", http.StatusBadRequest)
			return
		}

		client, err := a.findClient(r.Context(), clientID)
		if err != nil {
			http.Error(w, "unknown client", http.StatusUnauthorized)
			return
		}
		if !redirectAllowed(client.RedirectURIs, redirectURI) {
			http.Error(w, "redirect_uri is not registered", http.StatusUnauthorized)
			return
		}
	}

	// State khusus Google OAuth untuk mencegah CSRF.
	oauthState, err := randomToken(32)
	if err != nil {
		http.Error(w, "unable to prepare oauth state", http.StatusInternalServerError)
		return
	}

	// Simpan context flow ke cookie JWT agar callback Google bisa melanjutkan
	// ke flow authorize milik aplikasi asal.
	flowToken, err := a.makeOAuthFlowToken(clientID, redirectURI, appState, oauthState)
	if err != nil {
		http.Error(w, "unable to sign oauth state", http.StatusInternalServerError)
		return
	}

	http.SetCookie(w, &http.Cookie{
		Name:     a.oauthFlowCookie,
		Value:    flowToken,
		Path:     "/",
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
		Expires:  time.Now().Add(10 * time.Minute),
	})

	http.Redirect(w, r, a.googleOAuthConfig.AuthCodeURL(oauthState), http.StatusFound)
}

func (a *app) loginGoogleCallback(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	if a.googleOAuthConfig == nil || a.googleOAuthConfig.ClientID == "" || a.googleOAuthConfig.ClientSecret == "" {
		http.Error(w, "google oauth belum dikonfigurasi", http.StatusInternalServerError)
		return
	}

	// Jika user cancel/deny di Google, kembali ke login lokal SSO.
	if oauthErr := r.URL.Query().Get("error"); oauthErr != "" {
		http.Redirect(w, r, "/login", http.StatusFound)
		return
	}

	code := strings.TrimSpace(r.URL.Query().Get("code"))
	state := strings.TrimSpace(r.URL.Query().Get("state"))
	if code == "" || state == "" {
		http.Error(w, "invalid oauth callback", http.StatusBadRequest)
		return
	}

	flowCookie, err := r.Cookie(a.oauthFlowCookie)
	if err != nil {
		http.Error(w, "oauth state cookie tidak ditemukan", http.StatusUnauthorized)
		return
	}

	// Validasi state callback terhadap state yang disimpan.
	flowClaims, err := a.parseOAuthFlowToken(flowCookie.Value)
	if err != nil || flowClaims.OAuthState != state {
		http.Error(w, "oauth state tidak valid", http.StatusUnauthorized)
		return
	}

	// Tukar auth code Google menjadi access token Google.
	token, err := a.googleOAuthConfig.Exchange(r.Context(), code)
	if err != nil {
		http.Error(w, "gagal menukar google auth code", http.StatusUnauthorized)
		return
	}

	// Ambil profil user dari Google userinfo endpoint.
	googleProfile, err := a.fetchGoogleUserInfo(r.Context(), token.AccessToken)
	if err != nil {
		http.Error(w, "gagal mengambil profil google", http.StatusUnauthorized)
		return
	}

	if !googleProfile.EmailVerified {
		http.Error(w, "email google belum terverifikasi", http.StatusUnauthorized)
		return
	}

	if !a.isAllowedGoogleDomain(googleProfile.Email, googleProfile.HostedDomain) {
		http.Error(w, "hanya email domain pnc.ac.id yang diizinkan", http.StatusForbidden)
		return
	}

	// Sinkronisasi user ke tabel sso_users.
	user, err := a.findOrCreateGoogleUser(r.Context(), googleProfile)
	if err != nil {
		http.Error(w, "gagal menyiapkan user sso", http.StatusInternalServerError)
		return
	}

	sessionToken, err := a.makeSessionToken(user)
	if err != nil {
		http.Error(w, "gagal membuat sesi login", http.StatusInternalServerError)
		return
	}

	http.SetCookie(w, &http.Cookie{
		Name:     a.sessionCookie,
		Value:    sessionToken,
		Path:     "/",
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
		Expires:  time.Now().Add(8 * time.Hour),
	})

	http.SetCookie(w, &http.Cookie{
		Name:     a.oauthFlowCookie,
		Value:    "",
		Path:     "/",
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
		Expires:  time.Unix(0, 0),
		MaxAge:   -1,
	})

	// Jika callback berasal dari flow OAuth app,
	// lanjutkan ke /authorize agar app mendapat authorization code.
	if flowClaims.ClientID != "" && flowClaims.RedirectURI != "" {
		q := url.Values{}
		q.Set("client_id", flowClaims.ClientID)
		q.Set("redirect_uri", flowClaims.RedirectURI)
		if flowClaims.AppState != "" {
			q.Set("state", flowClaims.AppState)
		}
		http.Redirect(w, r, "/authorize?"+q.Encode(), http.StatusFound)
		return
	}

	http.Redirect(w, r, "/health", http.StatusFound)
}
