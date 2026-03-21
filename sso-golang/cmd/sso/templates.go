package main

import (
	"embed"
	"html/template"
	"net/http"
)

//go:embed templates/login.html
var templateFS embed.FS

var loginPageTemplate = template.Must(template.ParseFS(templateFS, "templates/login.html"))

// renderLogin merender halaman login dengan konteks client OAuth.
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

	_ = loginPageTemplate.Execute(w, map[string]string{
		"ClientID":      clientID,
		"ClientName":    clientName,
		"RedirectURI":   redirectURI,
		"State":         state,
		"Error":         errMsg,
		"AllowedDomain": a.googleHostedDomain,
	})
}
