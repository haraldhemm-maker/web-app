/**
 * L O G O U T   C O N T R O L L E R   
 * - Steuerung der Logout-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class LogoutController {
    /**
     * Konstruktor
     * @param {LogoutModel} model
     * @param {LogoutView} view
     * @param {Configuration} config
     */
    constructor (model, view, config) {
	    this.model = model;
	    this.view = view;
	    this.config = config;
	    	    
	    console.log('LOGOUT CONTROLLER');
    }
} // LogoutController
