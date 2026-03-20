package main

import (
	"context"
	"crypto/rand"
	"database/sql"
	"encoding/base64"
	"encoding/json"
	"errors"
	"log"
	"net/http"
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
	db                 *sql.DB
	jwtSecret          []byte
	sessionCookie      string
	oauthFlowCookie    string
	codeTTL            time.Duration
	tokenTTL           time.Duration
	googleOAuthConfig  *oauth2.Config
	googleHostedDomain string
}

// claims dipakai untuk session JWT internal dan access token OAuth.
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
	ClientID    string `json:"client_id"`
	RedirectURI string `json:"redirect_uri"`
	AppState    string `json:"app_state"`
	OAuthState  string `json:"oauth_state"`
	jwt.RegisteredClaims
}

type googleUserInfo struct {
	Email         string `json:"email"`
	EmailVerified bool   `json:"email_verified"`
	HostedDomain  string `json:"hd"`
	Name          string `json:"name"`
}

// main menginisialisasi dependency aplikasi, mendaftarkan route,
// lalu menjalankan HTTP server SSO.
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
		db:                 db,
		jwtSecret:          []byte(secret),
		sessionCookie:      sessionCookie,
		oauthFlowCookie:    oauthFlowCookie,
		codeTTL:            codeTTL,
		tokenTTL:           tokenTTL,
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
	// Endpoint utama SSO/OAuth.
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

// health dipakai untuk pengecekan status service.
func (a *app) health(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	_ = json.NewEncoder(w).Encode(map[string]string{"status": "ok"})
}

func (a *app) currentUser(r *http.Request) (ssoUser, error) {
	// Ambil user login dari cookie session JWT.
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

// redirectAllowed memeriksa apakah redirect URI ada di daftar whitelist client.
// Mendukung format JSON array, newline-separated, atau comma-separated.
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

// makeSessionToken membuat JWT untuk cookie session login SSO.
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

// makeAccessToken membuat JWT access token untuk OAuth client.
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

// parseToken memvalidasi signature JWT dan mengembalikan claims.
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

// makeOAuthFlowToken menyimpan konteks flow Google OAuth dalam JWT sementara.
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

// parseOAuthFlowToken memvalidasi JWT konteks flow OAuth.
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

// fetchGoogleUserInfo mengambil profil user dari Google OpenID endpoint.
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

// isAllowedGoogleDomain membatasi login Google ke domain kampus.
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

// findOrCreateGoogleUser mencari user existing berdasarkan email,
// atau membuat akun baru dengan role default "user".
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

// ensureSchema memastikan tabel inti SSO tersedia.
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

// ensureAdminUser membuat akun admin awal dari env bila belum ada.
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

// randomToken menghasilkan token acak URL-safe.
func randomToken(size int) (string, error) {
	b := make([]byte, size)
	if _, err := rand.Read(b); err != nil {
		return "", err
	}
	return base64.RawURLEncoding.EncodeToString(b), nil
}

// env membaca environment variable dengan fallback.
func env(key, fallback string) string {
	value := strings.TrimSpace(os.Getenv(key))
	if value == "" {
		return fallback
	}
	return value
}

// minutesEnv membaca env integer menit dan mengembalikan duration.
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

// withCORS menambahkan header CORS dasar untuk integrasi antar aplikasi.
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

// loadDotEnv membaca file .env sederhana untuk local development.
// Nilai env yang sudah ada tidak akan ditimpa.
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
