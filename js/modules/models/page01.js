/** 
 * P A G E 0 1   M O D E L   
 * - Modell der Seite 01.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class Page01Model {
    /**
     * Konstruktor
     * @param {Configuration} config Instanz des Konfiguration-Singleton. 
     * @param {Utilities} utils Instanz des Werkzeug-Singletons.
     */
    constructor (config, utils) {
	    this.config = config;
	    this.utils = utils;
	  
	    console.log('PAGE01 MODEL');
    }
} // Page01Model
