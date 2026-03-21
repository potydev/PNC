package main

import (
	"net/http"
	"net/url"
	"strings"
	"time"

	"golang.org/x/crypto/bcrypt"
)

// authorize menjalankan OAuth authorization endpoint.
// Jika user belum login, user dialihkan ke /login.
// Jika user sudah login, service menerbitkan authorization code lalu redirect kembali ke client.
func (a *app) authorize(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	clientID := r.URL.Query().Get("client_id")
	redirectURI := r.URL.Query().Get("redirect_uri")
	state := r.URL.Query().Get("state")
	if clientID == "" || redirectURI == "" {
		http.Error(w, "client_id and redirect_uri are required", http.StatusBadRequest)
		return
	}

	// Validasi client OAuth dan redirect URI yang diizinkan.
	client, err := a.findClient(r.Context(), clientID)
	if err != nil {
		http.Error(w, "unknown client", http.StatusUnauthorized)
		return
	}
	if !redirectAllowed(client.RedirectURIs, redirectURI) {
		http.Error(w, "redirect_uri is not registered", http.StatusUnauthorized)
		return
	}

	// Cek sesi user pada cookie JWT internal.
	user, err := a.currentUser(r)
	if err != nil {
		q := url.Values{}
		q.Set("client_id", clientID)
		q.Set("redirect_uri", redirectURI)
		q.Set("state", state)
		http.Redirect(w, r, "/login?"+q.Encode(), http.StatusFound)
		return
	}

	// Terbitkan auth code sekali pakai.
	code, err := randomToken(48)
	if err != nil {
		http.Error(w, "unable to generate auth code", http.StatusInternalServerError)
		return
	}

	expiresAt := time.Now().Add(a.codeTTL)
	_, err = a.db.ExecContext(r.Context(), `
		INSERT INTO sso_auth_codes (code, user_id, client_id, redirect_uri, expires_at)
		VALUES (?, ?, ?, ?, ?)
	`, code, user.ID, clientID, redirectURI, expiresAt)
	if err != nil {
		http.Error(w, "unable to issue auth code", http.StatusInternalServerError)
		return
	}

	// Sisipkan code dan state ke URL callback client.
	redir, err := url.Parse(redirectURI)
	if err != nil {
		http.Error(w, "invalid redirect_uri", http.StatusBadRequest)
		return
	}
	params := redir.Query()
	params.Set("code", code)
	if state != "" {
		params.Set("state", state)
	}
	redir.RawQuery = params.Encode()
	http.Redirect(w, r, redir.String(), http.StatusFound)
}

func (a *app) login(w http.ResponseWriter, r *http.Request) {
	switch r.Method {
	case http.MethodGet:
		a.renderLogin(w, r, "")
	case http.MethodPost:
		a.loginPost(w, r)
	default:
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
	}
}

func (a *app) loginPost(w http.ResponseWriter, r *http.Request) {
	if err := r.ParseForm(); err != nil {
		http.Error(w, "invalid form", http.StatusBadRequest)
		return
	}

	email := strings.TrimSpace(strings.ToLower(r.FormValue("email")))
	password := r.FormValue("password")
	clientID := r.FormValue("client_id")
	redirectURI := r.FormValue("redirect_uri")
	state := r.FormValue("state")

	// Verifikasi kredensial email/password di database SSO.
	user, err := a.findUserByEmail(r.Context(), email)
	if err != nil || bcrypt.CompareHashAndPassword([]byte(user.PasswordHash), []byte(password)) != nil {
		a.renderLogin(w, r, "Email atau password salah")
		return
	}

	token, err := a.makeSessionToken(user)
	if err != nil {
		http.Error(w, "unable to create session", http.StatusInternalServerError)
		return
	}

	http.SetCookie(w, &http.Cookie{
		Name:     a.sessionCookie,
		Value:    token,
		Path:     "/",
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
		Expires:  time.Now().Add(8 * time.Hour),
	})

	// Jika login dipanggil dalam flow OAuth app, lanjutkan ke authorize.
	if clientID != "" && redirectURI != "" {
		q := url.Values{}
		q.Set("client_id", clientID)
		q.Set("redirect_uri", redirectURI)
		if state != "" {
			q.Set("state", state)
		}
		http.Redirect(w, r, "/authorize?"+q.Encode(), http.StatusFound)
		return
	}

	http.Redirect(w, r, "/health", http.StatusFound)
}
