/**
 * I N F O   C O N T R O L L E R   - Steuerung der Info-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2023 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @class
 */
export class InfoController {
    /**
     * Konstruktor
     * @param {InfoModel} model
     * @param {InfoView} view
     * @param {Configuration} config Configuration singleton
     */
    constructor (model, view, config) {
	    this.model = model;
	    this.view = view;
	    this.config = config;
	    
	    this.model.setCurrentVersion(this.view.getVersionElement());
	    
	    console.log('INFO CONTROLLER');
    }
} // InfoController
