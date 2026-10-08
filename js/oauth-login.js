/**
 * oauth-login.js - Startet den OAuth2 Authorization Code Flow mit PKCE (S256)
 *
 * Klick auf "anmelden" erzeugt frische PKCE-Werte und leitet zum
 * Authorization-Server um.
 */

const OAUTH = {
  // Basis-URL des Authorization Servers (index.php)
  authorizeEndpoint: 'http://localhost:8000/index.php',
  // Registrierter öffentlicher Client (PKCE Pflicht)
  clientId: 'demo-app',
  // Muss exakt dem in der DB registrierten redirect_uri entsprechen!
  redirectUri: 'http://localhost/~harald/app/web-app/php/callback.php',
  scope: ''
};

/** Bytes -> Base64URL (ohne Padding, URL-sicher) */
function base64url(bytes) {
  let bin = '';
  bytes.forEach(b => (bin += String.fromCharCode(b)));
  return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

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

document.getElementById('oauth-login')
  .addEventListener('click', startLogin);