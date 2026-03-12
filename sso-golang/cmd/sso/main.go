package main

import (
	"context"
	"crypto/rand"
	"database/sql"
	"encoding/base64"
	"encoding/json"
	"errors"
	"html/template"
	"log"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"strings"
	"time"

	_ "github.com/go-sql-driver/mysql"
	"github.com/golang-jwt/jwt/v5"
	"golang.org/x/crypto/bcrypt"
	"golang.org/x/oauth2"
	"golang.org/x/oauth2/google"
)

type app struct {
	db            *sql.DB
	jwtSecret     []byte
	sessionCookie string
	oauthFlowCookie string
	codeTTL       time.Duration
	tokenTTL      time.Duration
	googleOAuthConfig *oauth2.Config
	googleHostedDomain string
}

type claims struct {
	UserID int64  `json:"uid"`
	Email  string `json:"email"`
	Name   string `json:"name"`
	Role   string `json:"role"`
	jwt.RegisteredClaims
}

type ssoUser struct {
	ID           int64
	Name         string
	Email        string
	PasswordHash string
	Role         string
}

type ssoClient struct {
	ClientID     string
	ClientSecret string
	Name         string
	RedirectURIs string
}

type oauthFlowClaims struct {
	ClientID       string `json:"client_id"`
	RedirectURI    string `json:"redirect_uri"`
	AppState       string `json:"app_state"`
	OAuthState     string `json:"oauth_state"`
	jwt.RegisteredClaims
}

type googleUserInfo struct {
	Email         string `json:"email"`
	EmailVerified bool   `json:"email_verified"`
	HostedDomain  string `json:"hd"`
	Name          string `json:"name"`
}

const loginTemplate = `<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SSO Login</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f6f7fb; }
    .card { width: 420px; margin: 64px auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 8px 30px rgba(0,0,0,.08); }
    h1 { margin-top: 0; font-size: 20px; }
    .muted { color: #666; font-size: 14px; margin-bottom: 16px; }
    label { display:block; font-size:14px; margin: 10px 0 6px; }
    input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
    button { margin-top: 16px; width: 100%; padding: 11px; border: 0; border-radius: 8px; background: #2563eb; color:#fff; font-weight: bold; cursor: pointer; }
		.btn-google { display:block; margin-top: 12px; width: 100%; padding: 11px; border-radius: 8px; border: 1px solid #ddd; text-align: center; text-decoration: none; color: #111827; font-weight: 600; }
    .err { margin-top: 10px; color: #b91c1c; font-size: 14px; }
    .client { background:#f3f4f6; padding: 8px 10px; border-radius: 8px; margin-bottom: 12px; font-size: 13px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Central SSO Login</h1>
    <div class="muted">Masuk sekali untuk semua aplikasi.</div>
    {{if .ClientName}}<div class="client">Aplikasi: <strong>{{.ClientName}}</strong></div>{{end}}
    <form method="post" action="/login">
      <input type="hidden" name="client_id" value="{{.ClientID}}" />
      <input type="hidden" name="redirect_uri" value="{{.RedirectURI}}" />
      <input type="hidden" name="state" value="{{.State}}" />

      <label>Email</label>
      <input type="email" name="email" required />

      <label>Password</label>
      <input type="password" name="password" required />

      <button type="submit">Login</button>
			<a class="btn-google" href="/login/google?client_id={{.ClientID}}&redirect_uri={{.RedirectURI}}&state={{.State}}">Login dengan Google (@{{.AllowedDomain}})</a>
      {{if .Error}}<div class="err">{{.Error}}</div>{{end}}
    </form>
  </div>
</body>
</html>`

func main() {
	_ = loadDotEnv(".env")

	addr := env("SSO_ADDR", ":8088")
	dsn := env("SSO_DB_DSN", "root:password@tcp(127.0.0.1:3306)/sso_central?parseTime=true")
	secret := env("SSO_JWT_SECRET", "change-this-super-secret-key")
	sessionCookie := env("SSO_SESSION_COOKIE", "sso_session")
	oauthFlowCookie := env("SSO_OAUTH_FLOW_COOKIE", "sso_oauth_flow")
	codeTTL := minutesEnv("SSO_CODE_TTL_MINUTES", 5)
	tokenTTL := minutesEnv("SSO_TOKEN_TTL_MINUTES", 60)
	googleClientID := env("GOOGLE_OAUTH_CLIENT_ID", "")
	googleClientSecret := env("GOOGLE_OAUTH_CLIENT_SECRET", "")
	googleRedirectURL := env("GOOGLE_OAUTH_REDIRECT_URL", "http://127.0.0.1:8088/login/google/callback")
	googleHostedDomain := strings.ToLower(env("GOOGLE_OAUTH_HOSTED_DOMAIN", "pnc.ac.id"))

	db, err := sql.Open("mysql", dsn)
	if err != nil {
		log.Fatalf("open db failed: %v", err)
	}
	defer db.Close()

	if err := db.Ping(); err != nil {
		log.Fatalf("db ping failed: %v", err)
	}

	if err := ensureSchema(db); err != nil {
		log.Fatalf("ensure schema failed: %v", err)
	}

	if err := ensureAdminUser(db); err != nil {
		log.Fatalf("ensure admin failed: %v", err)
	}

	a := &app{
		db:               db,
		jwtSecret:        []byte(secret),
		sessionCookie:    sessionCookie,
		oauthFlowCookie:  oauthFlowCookie,
		codeTTL:          codeTTL,
		tokenTTL:         tokenTTL,
		googleHostedDomain: googleHostedDomain,
		googleOAuthConfig: &oauth2.Config{
			ClientID:     googleClientID,
			ClientSecret: googleClientSecret,
			RedirectURL:  googleRedirectURL,
			Scopes:       []string{"openid", "profile", "email"},
			Endpoint:     google.Endpoint,
		},
	}

	mux := http.NewServeMux()
	mux.HandleFunc("/health", a.health)
	mux.HandleFunc("/authorize", a.authorize)
	mux.HandleFunc("/login", a.login)
	mux.HandleFunc("/login/google", a.loginGoogle)
	mux.HandleFunc("/login/google/callback", a.loginGoogleCallback)
	mux.HandleFunc("/token", a.token)
	mux.HandleFunc("/userinfo", a.userinfo)
	mux.HandleFunc("/logout", a.logout)

	log.Printf("SSO service listening on %s", addr)
	if err := http.ListenAndServe(addr, withCORS(mux)); err != nil {
		log.Fatalf("server failed: %v", err)
	}
}

func (a *app) health(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	_ = json.NewEncoder(w).Encode(map[string]string{"status": "ok"})
}

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

	client, err := a.findClient(r.Context(), clientID)
	if err != nil {
		http.Error(w, "unknown client", http.StatusUnauthorized)
		return
	}
	if !redirectAllowed(client.RedirectURIs, redirectURI) {
		http.Error(w, "redirect_uri is not registered", http.StatusUnauthorized)
		return
	}

	user, err := a.currentUser(r)
	if err != nil {
		q := url.Values{}
		q.Set("client_id", clientID)
		q.Set("redirect_uri", redirectURI)
		q.Set("state", state)
		http.Redirect(w, r, "/login?"+q.Encode(), http.StatusFound)
		return
	}

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

func (a *app) renderLogin(w http.ResponseWriter, r *http.Request, errMsg string) {
	clientID := r.URL.Query().Get("client_id")
	redirectURI := r.URL.Query().Get("redirect_uri")
	state := r.URL.Query().Get("state")
	clientName := ""
	if clientID != "" {
		if client, err := a.findClient(r.Context(), clientID); err == nil {
			clientName = client.Name
		}
	}

	tpl := template.Must(template.New("login").Parse(loginTemplate))
	_ = tpl.Execute(w, map[string]string{
		"ClientID":    clientID,
		"ClientName":  clientName,
		"RedirectURI": redirectURI,
		"State":       state,
		"Error":       errMsg,
		"AllowedDomain": a.googleHostedDomain,
	})
}

func (a *app) loginGoogle(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	if a.googleOAuthConfig == nil || a.googleOAuthConfig.ClientID == "" || a.googleOAuthConfig.ClientSecret == "" {
		http.Error(w, "google oauth belum dikonfigurasi", http.StatusInternalServerError)
		return
	}

	clientID := strings.TrimSpace(r.URL.Query().Get("client_id"))
	redirectURI := strings.TrimSpace(r.URL.Query().Get("redirect_uri"))
	appState := strings.TrimSpace(r.URL.Query().Get("state"))

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

	oauthState, err := randomToken(32)
	if err != nil {
		http.Error(w, "unable to prepare oauth state", http.StatusInternalServerError)
		return
	}

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

	flowClaims, err := a.parseOAuthFlowToken(flowCookie.Value)
	if err != nil || flowClaims.OAuthState != state {
		http.Error(w, "oauth state tidak valid", http.StatusUnauthorized)
		return
	}

	token, err := a.googleOAuthConfig.Exchange(r.Context(), code)
	if err != nil {
		http.Error(w, "gagal menukar google auth code", http.StatusUnauthorized)
		return
	}

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

func (a *app) token(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "method not allowed", http.StatusMethodNotAllowed)
		return
	}

	if err := r.ParseForm(); err != nil {
		http.Error(w, "invalid form", http.StatusBadRequest)
		return
	}

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

	if usedAt.Valid || expiresAt.Before(time.Now()) || dbClientID != clientID || dbRedirectURI != redirectURI {
		http.Error(w, "invalid code", http.StatusUnauthorized)
		return
	}

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

func (a *app) currentUser(r *http.Request) (ssoUser, error) {
	cookie, err := r.Cookie(a.sessionCookie)
	if err != nil {
		return ssoUser{}, err
	}
	claims, err := a.parseToken(cookie.Value)
	if err != nil {
		return ssoUser{}, err
	}

	var user ssoUser
	err = a.db.QueryRowContext(r.Context(), `
		SELECT id, name, email, password_hash, role
		FROM sso_users WHERE id = ?
	`, claims.UserID).Scan(&user.ID, &user.Name, &user.Email, &user.PasswordHash, &user.Role)
	if err != nil {
		return ssoUser{}, err
	}
	return user, nil
}

func (a *app) findUserByEmail(ctx context.Context, email string) (ssoUser, error) {
	var user ssoUser
	err := a.db.QueryRowContext(ctx, `
		SELECT id, name, email, password_hash, role
		FROM sso_users
		WHERE email = ?
	`, email).Scan(&user.ID, &user.Name, &user.Email, &user.PasswordHash, &user.Role)
	return user, err
}

func (a *app) findClient(ctx context.Context, clientID string) (ssoClient, error) {
	var client ssoClient
	err := a.db.QueryRowContext(ctx, `
		SELECT client_id, client_secret, name, redirect_uris
		FROM sso_clients
		WHERE client_id = ?
	`, clientID).Scan(&client.ClientID, &client.ClientSecret, &client.Name, &client.RedirectURIs)
	return client, err
}

func redirectAllowed(allowedRaw, redirectURI string) bool {
	allowedRaw = strings.TrimSpace(allowedRaw)
	if allowedRaw == "" {
		return false
	}
	if strings.HasPrefix(allowedRaw, "[") {
		var values []string
		if err := json.Unmarshal([]byte(allowedRaw), &values); err == nil {
			for _, v := range values {
				if strings.TrimSpace(v) == redirectURI {
					return true
				}
			}
		}
	}
	for _, line := range strings.Split(allowedRaw, "\n") {
		if strings.TrimSpace(line) == redirectURI {
			return true
		}
	}
	for _, part := range strings.Split(allowedRaw, ",") {
		if strings.TrimSpace(part) == redirectURI {
			return true
		}
	}
	return false
}

func (a *app) makeSessionToken(user ssoUser) (string, error) {
	c := claims{
		UserID: user.ID,
		Email:  user.Email,
		Name:   user.Name,
		Role:   user.Role,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(8 * time.Hour)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
			Issuer:    "sso-golang",
		},
	}
	return jwt.NewWithClaims(jwt.SigningMethodHS256, c).SignedString(a.jwtSecret)
}

func (a *app) makeAccessToken(user ssoUser, audience string) (string, error) {
	c := claims{
		UserID: user.ID,
		Email:  user.Email,
		Name:   user.Name,
		Role:   user.Role,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(a.tokenTTL)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
			Issuer:    "sso-golang",
			Audience:  []string{audience},
		},
	}
	return jwt.NewWithClaims(jwt.SigningMethodHS256, c).SignedString(a.jwtSecret)
}

func (a *app) parseToken(token string) (claims, error) {
	parsed, err := jwt.ParseWithClaims(token, &claims{}, func(t *jwt.Token) (any, error) {
		if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, errors.New("unexpected signing method")
		}
		return a.jwtSecret, nil
	})
	if err != nil {
		return claims{}, err
	}
	c, ok := parsed.Claims.(*claims)
	if !ok || !parsed.Valid {
		return claims{}, errors.New("invalid token")
	}
	return *c, nil
}

func (a *app) makeOAuthFlowToken(clientID, redirectURI, appState, oauthState string) (string, error) {
	flowClaims := oauthFlowClaims{
		ClientID:    clientID,
		RedirectURI: redirectURI,
		AppState:    appState,
		OAuthState:  oauthState,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(10 * time.Minute)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
			Issuer:    "sso-golang",
		},
	}

	return jwt.NewWithClaims(jwt.SigningMethodHS256, flowClaims).SignedString(a.jwtSecret)
}

func (a *app) parseOAuthFlowToken(token string) (oauthFlowClaims, error) {
	parsed, err := jwt.ParseWithClaims(token, &oauthFlowClaims{}, func(t *jwt.Token) (any, error) {
		if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, errors.New("unexpected signing method")
		}
		return a.jwtSecret, nil
	})
	if err != nil {
		return oauthFlowClaims{}, err
	}

	flow, ok := parsed.Claims.(*oauthFlowClaims)
	if !ok || !parsed.Valid {
		return oauthFlowClaims{}, errors.New("invalid oauth flow token")
	}

	return *flow, nil
}

func (a *app) fetchGoogleUserInfo(ctx context.Context, accessToken string) (googleUserInfo, error) {
	req, err := http.NewRequestWithContext(ctx, http.MethodGet, "https://openidconnect.googleapis.com/v1/userinfo", nil)
	if err != nil {
		return googleUserInfo{}, err
	}
	req.Header.Set("Authorization", "Bearer "+accessToken)

	resp, err := http.DefaultClient.Do(req)
	if err != nil {
		return googleUserInfo{}, err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return googleUserInfo{}, errors.New("google userinfo request failed")
	}

	var profile googleUserInfo
	if err := json.NewDecoder(resp.Body).Decode(&profile); err != nil {
		return googleUserInfo{}, err
	}

	profile.Email = strings.TrimSpace(strings.ToLower(profile.Email))
	profile.HostedDomain = strings.TrimSpace(strings.ToLower(profile.HostedDomain))
	profile.Name = strings.TrimSpace(profile.Name)
	return profile, nil
}

func (a *app) isAllowedGoogleDomain(email, hostedDomain string) bool {
	allowed := strings.TrimSpace(strings.ToLower(a.googleHostedDomain))
	if allowed == "" {
		return true
	}

	if hostedDomain == allowed {
		return true
	}

	parts := strings.Split(email, "@")
	if len(parts) != 2 {
		return false
	}

	return strings.ToLower(parts[1]) == allowed
}

func (a *app) findOrCreateGoogleUser(ctx context.Context, profile googleUserInfo) (ssoUser, error) {
	user, err := a.findUserByEmail(ctx, profile.Email)
	if err == nil {
		return user, nil
	}
	if !errors.Is(err, sql.ErrNoRows) {
		return ssoUser{}, err
	}

	name := profile.Name
	if name == "" {
		name = strings.Split(profile.Email, "@")[0]
	}

	randomPassword, err := randomToken(24)
	if err != nil {
		return ssoUser{}, err
	}

	hash, err := bcrypt.GenerateFromPassword([]byte(randomPassword), bcrypt.DefaultCost)
	if err != nil {
		return ssoUser{}, err
	}

	_, err = a.db.ExecContext(ctx, `
		INSERT INTO sso_users (name, email, password_hash, role)
		VALUES (?, ?, ?, ?)
	`, name, profile.Email, string(hash), "user")
	if err != nil {
		return ssoUser{}, err
	}

	return a.findUserByEmail(ctx, profile.Email)
}

func ensureSchema(db *sql.DB) error {
	stmts := []string{
		`CREATE TABLE IF NOT EXISTS sso_users (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			email VARCHAR(255) NOT NULL UNIQUE,
			password_hash VARCHAR(255) NOT NULL,
			role VARCHAR(64) NOT NULL DEFAULT 'user',
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		)`,
		`CREATE TABLE IF NOT EXISTS sso_clients (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			client_id VARCHAR(128) NOT NULL UNIQUE,
			client_secret VARCHAR(255) NOT NULL,
			name VARCHAR(255) NOT NULL,
			redirect_uris TEXT NOT NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		)`,
		`CREATE TABLE IF NOT EXISTS sso_auth_codes (
			code VARCHAR(128) PRIMARY KEY,
			user_id BIGINT UNSIGNED NOT NULL,
			client_id VARCHAR(128) NOT NULL,
			redirect_uri TEXT NOT NULL,
			expires_at DATETIME NOT NULL,
			used_at DATETIME NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			CONSTRAINT fk_auth_codes_user FOREIGN KEY (user_id) REFERENCES sso_users(id) ON DELETE CASCADE,
			CONSTRAINT fk_auth_codes_client FOREIGN KEY (client_id) REFERENCES sso_clients(client_id) ON DELETE CASCADE
		)`,
	}

	for _, stmt := range stmts {
		if _, err := db.Exec(stmt); err != nil {
			if strings.Contains(err.Error(), "Duplicate") || strings.Contains(err.Error(), "duplicate") {
				continue
			}
			return err
		}
	}
	return nil
}

func ensureAdminUser(db *sql.DB) error {
	email := strings.TrimSpace(strings.ToLower(os.Getenv("SSO_ADMIN_EMAIL")))
	password := os.Getenv("SSO_ADMIN_PASSWORD")
	if email == "" || password == "" {
		return nil
	}

	var count int
	if err := db.QueryRow("SELECT COUNT(1) FROM sso_users WHERE email = ?", email).Scan(&count); err != nil {
		return err
	}
	if count > 0 {
		return nil
	}

	hash, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	if err != nil {
		return err
	}

	_, err = db.Exec(`
		INSERT INTO sso_users (name, email, password_hash, role)
		VALUES (?, ?, ?, ?)
	`, "SSO Admin", email, string(hash), "admin")
	return err
}

func randomToken(size int) (string, error) {
	b := make([]byte, size)
	if _, err := rand.Read(b); err != nil {
		return "", err
	}
	return base64.RawURLEncoding.EncodeToString(b), nil
}

func env(key, fallback string) string {
	value := strings.TrimSpace(os.Getenv(key))
	if value == "" {
		return fallback
	}
	return value
}

func minutesEnv(key string, fallback int) time.Duration {
	value := strings.TrimSpace(os.Getenv(key))
	if value == "" {
		return time.Duration(fallback) * time.Minute
	}
	minutes, err := strconv.Atoi(value)
	if err != nil || minutes <= 0 {
		return time.Duration(fallback) * time.Minute
	}
	return time.Duration(minutes) * time.Minute
}

func withCORS(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
		w.Header().Set("Access-Control-Allow-Methods", "GET, POST, OPTIONS")
		if r.Method == http.MethodOptions {
			w.WriteHeader(http.StatusNoContent)
			return
		}
		next.ServeHTTP(w, r)
	})
}

func loadDotEnv(path string) error {
	content, err := os.ReadFile(path)
	if err != nil {
		return err
	}

	lines := strings.Split(string(content), "\n")
	for _, line := range lines {
		line = strings.TrimSpace(line)
		if line == "" || strings.HasPrefix(line, "#") {
			continue
		}

		parts := strings.SplitN(line, "=", 2)
		if len(parts) != 2 {
			continue
		}

		key := strings.TrimSpace(parts[0])
		value := strings.TrimSpace(parts[1])
		value = strings.Trim(value, "\"")
		if key == "" {
			continue
		}

		if _, exists := os.LookupEnv(key); !exists {
			_ = os.Setenv(key, value)
		}
	}

	return nil
}
