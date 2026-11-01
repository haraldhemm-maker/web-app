/** 
 * P R O F I L E   ( U S E R )   V I E W   
 * - Ansicht der Profilseite des Benutzers
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
import {ProxyView} from './proxy.js';
export class ProfileView extends ProxyView  {
    /**
     * Identifikationszeichenkette der "Seite".
     * @const {String}
     */
    PAGE_ID = 'profile';

    
	/**
	 * Konstruktor
	 */
	constructor () {
   		super();
   		this.setProxyTarget(this).createProxy();
   		this.PAGE_ID = 'profile'; // Initialisierung der (Pseudo-)Konstante.
   		
		console.log('PROFILE VIEW');
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
     * Holt das HTML-Element des Profilbildes.
     * 
     * @returns {HTMLImageElement} 
     */
    getProfileImgElement() {
        return window.document.querySelector('img.profile');
    }
    
    
    /**
     * Ueberprueft, ob ein Bild existiert.
     * 
     * @param {String} url
     * @returns {Boolean} Wenn das Bild existiert, dann liefert die Methode 
     *  den Wert true, sonst false.
     */
    async imgExists(url) {
        return new Promise((resolve) => {
            const test = new Image();
            test.onload  = () => resolve(true);
            test.onerror = () => resolve(false);
            test.src = url;
        });
    }
} // ProfileView
