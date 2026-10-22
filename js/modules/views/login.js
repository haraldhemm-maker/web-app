/** 
 * P A G E 0 1   V I E W   
 * - Ansicht der Seite 01.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
import {ProxyView} from './proxy.js';
export class LoginView extends ProxyView  {
    /**
     * Identifikationszeichenkette der "Seite".
     * @const {String}
     */
    PAGE_ID = 'login';

    
	/**
	 * Konstruktor
	 */
	constructor() {
   		super();
   		this.setProxyTarget(this).createProxy();
   		this.PAGE_ID = 'login'; // Initialisierung der (Pseudo-)Konstante.
   		
		console.log('LOGIN VIEW');
	}
    
    
    /**
     * Bearbeitung des Anmelde-Links.
     * 
     * @param {Boolean} available Wenn der Anmelde-Link verfuegbar sein soll, 
     *  dann muss der Wert dieses Parameters true sein. Ist der Wert des 
     *  Parameters false, dann wird statt eines Links ein Warnhinweis 
     *  angezeigt.
     * @return {LoginView}
     */
    handlingLoginLink(available) {
        const link = this.proxy.elementOauth;
        if (available) {
            // Server läuft: Link normal nutzen
            link.classList.remove('text-warning', 'text-danger');
            link.title = 'Anmelden';
 
        }
        else {
            // Server offline: Link sperren und kennzeichnen
            link.textContent = 'OAuth2-Server offline';
            link.classList.add('text-warning');
            link.title = 'Anmeldung nicht möglich - OAuth2-Server starten!';
            link.style.pointerEvents = 'none';   // Klicks blockieren
            link.style.textDecoration = 'none';  
        }
        return this;
    }
} // LoginView
