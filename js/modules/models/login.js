/** 
 * L O G I N   M O D E L   
 * - Modell der Login-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class LoginModel {
    /**
     * Konstruktor
     * @param {Configuration} config Instanz des Konfiguration-Singleton. 
     * @param {Utilities} utils Instanz des Werkzeug-Singletons.
     */
    constructor(config, utils) {
	    this.config = config;
	    this.utils = utils;
        
	    console.log('LOGIN MODEL');
    }
    
    
    /** 
     * Bytes -> Base64URL (ohne Padding, URL-sicher) 
     * 
     * @param {String} bytes Binary string
     * @returns {String} Base64-encoded ASCII string
     */
    base64url(bytes) {
      let bin = '';
      bytes.forEach(b => (bin += String.fromCharCode(b)));
      return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }
    
    
    /**
     * Login starten (Authorization Code Flow + PKCE)
     * 
     * @returns {LoginModel}
     */
    async startLogin() {
        // 1. PKCE: code_verifier (43-128 Zeichen, kryptografisch zufaellig).
        const verifier = this.base64url(
            crypto.getRandomValues(new Uint8Array(48))
        );
        const state = this.base64url(
            crypto.getRandomValues(new Uint8Array(24))
        );
        
        // 2. code_challenge = BASE64URL(SHA256(code_verifier)).
        const digest  = await crypto.subtle.digest('SHA-256',
                            new TextEncoder().encode(verifier));
        const challenge = this.base64url(new Uint8Array(digest));

        // 3. Fuer den Callback aufbewahren (gleiche Origin wie die 
        //  Callback-Seite).
        sessionStorage.setItem('oauth_code_verifier', verifier);
        sessionStorage.setItem('oauth_state', state);
        
        // 4. Umleitung zum Authorization Server
        const url = new URL(this.config.AUTH.URL_ENDPOINT);
        url.searchParams.set('action', 'authorize');
        url.searchParams.set('response_type', 'code');
        url.searchParams.set('client_id', this.config.CLIENT.ID);
        url.searchParams.set('redirect_uri', this.config.CLIENT.REDIRECT_URI);
        url.searchParams.set('state', state);
        url.searchParams.set('code_challenge', challenge);
        url.searchParams.set('code_challenge_method', 'S256');
        if (this.config.AUTH.SCOPE) {
            url.searchParams.set('scope', this.config.AUTH.SCOPE);
        }
        window.location.assign(url.toString());
        return this;
    }
    
    
    /**
     * Registrierung starten
     * 
     * @returns {void}
     */
    async startRegister() {
        // 1. PKCE: code_verifier (43-128 Zeichen, kryptografisch zufaellig).
        const verifier = this.base64url(
            crypto.getRandomValues(new Uint8Array(48))
        );
        const state = this.base64url(
            crypto.getRandomValues(new Uint8Array(24))
        );

        // 2. code_challenge = BASE64URL(SHA256(code_verifier)).
        const digest  = await crypto.subtle.digest('SHA-256',
                            new TextEncoder().encode(verifier));
        const challenge = this.base64url(new Uint8Array(digest));

        // 3. Fuer den Callback aufbewahren (gleiche Origin wie die 
        //  Callback-Seite).
        sessionStorage.setItem('oauth_code_verifier', verifier);
        sessionStorage.setItem('oauth_state', state);
        
        const url = new URL(this.config.AUTH.URL_ENDPOINT);
        url.searchParams.set('action', 'authorize');
        url.searchParams.set('response_type', 'code');
        url.searchParams.set('client_id', this.config.CLIENT.ID);
        url.searchParams.set('redirect_uri', this.config.CLIENT.REDIRECT_URI);
        url.searchParams.set('state', state);
        url.searchParams.set('code_challenge', challenge);
        url.searchParams.set('code_challenge_method', 'S256');
        if (this.config.AUTH.SCOPE) {
            url.searchParams.set('scope', this.config.AUTH.SCOPE);
        }
        window.location.assign(url.toString());
    }
    
    
    /**
     * Status-Check: laeuft der OAuth2-Server?
     * 
     * @returns {Boolean} Wenn der OAuth2-Server laeuft, dann liefert die 
     *  Methode den boolschen Wert true zurueck, sonst false.
     */
    async checkOauthServer() {
        try {
            const response = await fetch(
                this.config.AUTH.URL_STATUS_ENDPOINT, 
                {cache: 'no-store'}
            );
            const data = await response.json();
            if (data.online) {
                // Server laeuft; Server online
                return true;
            }
        } catch (e) {
            // Status-Endpoint selbst nicht erreichbar -> wie offline behandeln
            console.error(e);
            return false;
        }
        // Server offline
        return false;
    }
} // LoginModel
