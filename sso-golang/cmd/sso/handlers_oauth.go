package main

import (
	"database/sql"
	"encoding/json"
	"net/http"
	"strings"
	"time"
)

func (a *app) token(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	if err := r.ParseForm(); err != nil {
		http.Error(w, "invalid form", http.StatusBadRequest)
		return
	}

	// Service hanya mendukung grant_type authorization_code.
	if r.FormValue("grant_type") != "authorization_code" {
		http.Error(w, "unsupported grant_type", http.StatusBadRequest)
		return
	}

	code := r.FormValue("code")
	clientID := r.FormValue("client_id")
	clientSecret := r.FormValue("client_secret")
	redirectURI := r.FormValue("redirect_uri")
	if code == "" || clientID == "" || clientSecret == "" || redirectURI == "" {
		http.Error(w, "missing required fields", http.StatusBadRequest)
		return
	}

	// Validasi kredensial client OAuth.
	client, err := a.findClient(r.Context(), clientID)
	if err != nil || client.ClientSecret != clientSecret {
		http.Error(w, "invalid client", http.StatusUnauthorized)
		return
	}

	tx, err := a.db.BeginTx(r.Context(), nil)
	if err != nil {
		http.Error(w, "internal error", http.StatusInternalServerError)
		return
	}
	defer tx.Rollback()

	var userID int64
	var dbClientID string
	var dbRedirectURI string
	var expiresAt time.Time
	var usedAt sql.NullTime
	err = tx.QueryRowContext(r.Context(), `
		SELECT user_id, client_id, redirect_uri, expires_at, used_at
		FROM sso_auth_codes
		WHERE code = ?
		FOR UPDATE
	`, code).Scan(&userID, &dbClientID, &dbRedirectURI, &expiresAt, &usedAt)
	if err != nil {
		http.Error(w, "invalid code", http.StatusUnauthorized)
		return
	}

	// Pastikan auth code belum dipakai, belum kadaluarsa,
	// dan terikat ke client serta redirect URI yang sama.
	if usedAt.Valid || expiresAt.Before(time.Now()) || dbClientID != clientID || dbRedirectURI != redirectURI {
		http.Error(w, "invalid code", http.StatusUnauthorized)
		return
	}

	// Mark code as used
	_, err = tx.ExecContext(r.Context(), "UPDATE sso_auth_codes SET used_at = ? WHERE code = ?", time.Now(), code)
	if err != nil {
		http.Error(w, "unable to consume code", http.StatusInternalServerError)
		return
	}

	var user ssoUser
	err = tx.QueryRowContext(r.Context(), "SELECT id, name, email, role FROM sso_users WHERE id = ?", userID).
		Scan(&user.ID, &user.Name, &user.Email, &user.Role)
	if err != nil {
		http.Error(w, "invalid user", http.StatusUnauthorized)
		return
	}

	if err := tx.Commit(); err != nil {
		http.Error(w, "internal error", http.StatusInternalServerError)
		return
	}

	// Buat access token JWT untuk dipakai ke endpoint /userinfo.
	accessToken, err := a.makeAccessToken(user, clientID)
	if err != nil {
		http.Error(w, "unable to sign token", http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	_ = json.NewEncoder(w).Encode(map[string]any{
		"token_type":   "Bearer",
		"access_token": accessToken,
		"expires_in":   int(a.tokenTTL.Seconds()),
	})
}

func (a *app) userinfo(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	// Terima bearer token dari Authorization header.
	token := strings.TrimSpace(strings.TrimPrefix(r.Header.Get("Authorization"), "Bearer"))
	if token == "" {
		http.Error(w, "missing bearer token", http.StatusUnauthorized)
		return
	}

	parsedClaims, err := a.parseToken(token)
	if err != nil {
		http.Error(w, "invalid token", http.StatusUnauthorized)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	_ = json.NewEncoder(w).Encode(map[string]any{
		"id":    parsedClaims.UserID,
		"name":  parsedClaims.Name,
		"email": parsedClaims.Email,
		"role":  parsedClaims.Role,
	})
}

func (a *app) logout(w http.ResponseWriter, r *http.Request) {
	// Hapus cookie session SSO lalu redirect ke halaman tujuan.
	next := r.URL.Query().Get("redirect")
	http.SetCookie(w, &http.Cookie{
		Name:     a.sessionCookie,
		Value:    "",
		Path:     "/",
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
		Expires:  time.Unix(0, 0),
		MaxAge:   -1,
	})
	if next == "" {
		next = "/login"
	}
	http.Redirect(w, r, next, http.StatusFound)
}
