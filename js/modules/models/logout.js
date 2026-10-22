/** 
 * L O G O U T   M O D E L   
 * - Modell der Logout-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class LogoutModel {
    /**
     * Konstruktor
     * @param {Configuration} config Instanz des Konfiguration-Singleton. 
     * @param {Utilities} utils Instanz des Werkzeug-Singletons.
     */
    constructor (config, utils) {
	    this.config = config;
	    this.utils = utils;
	  
	    console.log('LOGOUT MODEL');
    }
} // LogoutModel
