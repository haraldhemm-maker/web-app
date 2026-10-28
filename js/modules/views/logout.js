/** 
 * L O G O U T   V I E W   
 *  - Ansicht der Logout-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
import {ProxyView} from './proxy.js';
export class LogoutView extends ProxyView  {
    /**
     * Identifikationszeichenkette der "Seite".
     * @const {String}
     */
    PAGE_ID = 'logout';
    
	/**
	 * Konstruktor
	 */
	constructor(config, utils, client) {
   		super();
   		this.setProxyTarget(this).createProxy();
   		this.PAGE_ID = 'logout'; // Initialisierung der (Pseudo-)Konstante.
        this.config = config;
        this.utils = utils; 
        this.client = client;
        
        this.setCounting(10);
        this.setProgressBar(0);
   		
		console.log('LOGOUT VIEW');
	}
    
    
    /**
     * Holt das HTLMElement des Abbrechen-Knopfes.
     * @returns {HTMLInputElement} Abbrechen-Knopf
     */
    getCancelButton () {
        return document.getElementById('logout_cancel');
    }

    
    /**
     * Setzt den Text des Zaehlerelements auf den im Parameter uebergebenen 
     *  Zaehlerwert.
     *
     * @params {Number} reverseCounter Abnehmender Wert des Zaehlers.
     * @returns {LogoutView} fluent interface.
     */
    setCounting (reverseCounter) {
        document.getElementById('logout_counting').textContent = reverseCounter;
        return this;
    }
    
    
    /**
     * Startet das Abspielen des Schulgongs. Die Abspielzeit beträgt 10 
     *  Sekunden.
     * 
     * @returns {LogoutView} fluent interface.
     */
    startPlayingSchoolBell() {
        this.audio = new Audio('aud/schulgong_10s.mp3');
        this.audio.play();
        return this;
    }
    
    
    /**
     * Stoppt das Abspiele des Schulgongs.
     * 
     * @returns {LogoutView} fluent interface.
     */
    stopPlayingSchoolBell() {
        this.audio.pause();
        this.audio.currentTime = 0;
        return this;   
    }
    
    
    /**
     * Setzt den Wert des Fortschrittbalkens auf den im Parameter uebergebenen 
     *  Zaehlerwert.
     *
     * @params {Number} reverseCounter Abnehmender Wert des Zaehlers.
     * @returns {LogoutView} fluent interface.
     */
    setProgressBar (reverseCounter) {
        document.getElementById('logout_progressbar').value = reverseCounter;
        return this;
    }
} // LogoutView
