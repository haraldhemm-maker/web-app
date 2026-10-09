/**
 * oauth-login.js - Startet den OAuth2 Authorization Code Flow mit PKCE (S256)
 *
 * Erweiterung: Beim Laden wird geprüft, ob der OAuth2-Server erreichbar ist
 * (php/oauth-status.php). Ist er offline, wird der "anmelden"-Link
 * deaktiviert und farblich markiert.
 */


const OAUTH = {
  // Basis-URL des Authorization Servers (index.php)
  authorizeEndpoint: 'http://localhost:8000/index.php',
  // Registrierter öffentlicher Client (PKCE Pflicht)
  clientId: 'web-app',
  // Muss exakt dem in der DB registrierten redirect_uri entsprechen!
  redirectUri: 'http://localhost/~harald/app/web-app/php/callback.php',
  // Status-Endpoint (serverseitiger Check, umgeht CORS)
  statusEndpoint: './php/oauth-status.php',
  scope: ''
};


/** Bytes -> Base64URL (ohne Padding, URL-sicher) */
function base64url(bytes) {
  let bin = '';
  bytes.forEach(b => (bin += String.fromCharCode(b)));
  return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}


/* ---------------------------------------------------------
 * Status-Check: läuft der OAuth2-Server?
 * --------------------------------------------------------- */
async function checkOauthServer() {
  const link = document.getElementById('oauth-login');
  if (!link) return;

  try {
    const res   = await fetch(OAUTH.statusEndpoint, { cache: 'no-store' });
    const data  = await res.json();

    if (data.online) {
      // Server läuft: Link normal nutzen
      link.classList.remove('text-warning', 'text-danger');
      link.title = 'Anmelden';
      return;
    }
  } catch (e) {
    // Status-Endpoint selbst nicht erreichbar -> wie offline behandeln
  }

  
  // Server offline: Link sperren und kennzeichnen
  link.textContent = 'OAuth2-Server offline';
  link.classList.add('text-warning');
  link.title = 'Anmeldung nicht möglich - OAuth2-Server starten!';
  link.style.pointerEvents = 'none';   // Klicks blockieren
  link.style.textDecoration = 'none';
}


/* ---------------------------------------------------------
 * Login starten (Authorization Code Flow + PKCE)
 * --------------------------------------------------------- */
async function startLogin(event) {
  event.preventDefault();

  // 1. PKCE: code_verifier (43-128 Zeichen, kryptografisch zufällig)
  const verifier  = base64url(crypto.getRandomValues(new Uint8Array(48)));
  const state    = base64url(crypto.getRandomValues(new Uint8Array(24)));

  // 2. code_challenge = BASE64URL(SHA256(code_verifier))
  const digest    = await crypto.subtle.digest('SHA-256',
                     new TextEncoder().encode(verifier));
  const challenge = base64url(new Uint8Array(digest));

  // 3. Für den Callback aufbewahren (gleiche Origin wie die Callback-Seite)
  sessionStorage.setItem('oauth_code_verifier', verifier);
  sessionStorage.setItem('oauth_state', state);

  // 4. Umleitung zum Authorization Server
  const url = new URL(OAUTH.authorizeEndpoint);
  url.searchParams.set('action', 'authorize');
  url.searchParams.set('response_type', 'code');
  url.searchParams.set('client_id', OAUTH.clientId);
  url.searchParams.set('redirect_uri', OAUTH.redirectUri);
  url.searchParams.set('state', state);
  url.searchParams.set('code_challenge', challenge);
  url.searchParams.set('code_challenge_method', 'S256');
  if (OAUTH.scope) {
    url.searchParams.set('scope', OAUTH.scope);
  }
  window.location.assign(url.toString());
}


/* Initialisierung */
document.getElementById('oauth-login')
  .addEventListener('click', startLogin);

checkOauthServer();