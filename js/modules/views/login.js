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
     * Zeige ein HTML-Element.
     * 
     * @param {HTMLElement} elem Ein HTML-Element, das angezeigt werden soll.
     * @return {LoginView}
     */
    showElement(elem) {
        elem.removeAttribute('style');
        return this;
    }
    
    
    /**
     * Verstecke ein HTML-Element.
     * 
     * @param {HTMLElement} elem Ein HTML-Element, das versteckt werden soll.
     * @return {LoginView}
     */
    hideElement(elem) {
        elem.setAttribute('style', 'display:none');
        return this;
    }
    
        
    /**
     * Bearbeitung des Anmelde- und Registrieren-Knopfes.
     * 
     * @param {Boolean} available Wenn der Anmelde- und Registrieren-Knopf 
     *  verfuegbar sein soll, dann muss der Wert dieses Parameters true sein. 
     *  Ist der Wert des Parameters false, dann werden die Knoepfe deaktiviert 
     *  und ein Warnhinweis angezeigt.
     * @return {LoginView}
     */
    handlingLoginAndRegisterBtn(available) {
        if (available) {
            // Schaltknoepfe aktivieren.
            this.proxy.elementOauth.disabled = false;
            this.proxy.elementOauthRegister.disabled = false;
            // Warnmeldung verstecken.
            this.hideElement(this.proxy.elementAlertBox);
        }
        else {
            // Schaltknoepfe deaktivieren.
            this.proxy.elementOauth.disabled = true;
            this.proxy.elementOauthRegister.disabled = true;
            // Warnmeldung anzeigen
            this.showElement(this.proxy.elementAlertBox);
        }
        return this;
    }
} // LoginView
