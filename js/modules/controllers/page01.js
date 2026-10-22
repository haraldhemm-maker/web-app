/**
 * P A G E 0 1   C O N T R O L L E R   
 * - Steuerung der Seite 01.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class Page01Controller {
    /**
     * Konstruktor
     * @param {Page01Model} model
     * @param {Page01View} view
     * @param {Configuration} config
     */
    constructor (model, view, config) {
	    this.model = model;
	    this.view = view;
	    this.config = config;
	    	    
	    console.log('PAGE01 CONTROLLER');
    }
} // Page01Controller
