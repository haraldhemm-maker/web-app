/** 
 * I N F O   M O D E L   - Modell der Info-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2023 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class InfoModel {
    /**
     * Konstruktor
     * 
     * @param {Configuration} config Instanz des Konfiguration-Singleton. 
     * @param {Utilities} utils Instanz des Werkzeug-Singletons.
     * @param {HttpRestClient} client Instanz des HTTP-REST-Klienten.
     */
    constructor (config, utils, client) {
	    this.config = config;
	    this.utils = utils;
	    this.client = client;
	  
	    console.log('INFO MODEL');
    }
  
  
    /**
     * Setzt die aktuelle Versionsangabe auf der Info-Seite.
     * 
     * @param {HTMLElement} HTML-Element, das die Versionangaben enthalten soll.
     * @returns {InfoModel} Gibt das Objekt selbst zurueck ("sprechende Schnittstelle").
     */ 
    setCurrentVersion (element) {
	    element.textContent += this.config.INFO.VERSION;
	    return this;
    }
} // InfoModel
