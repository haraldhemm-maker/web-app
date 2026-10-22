/** 
 * I N F O   V I E W   - Ansicht der Info-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2023 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @class
 */
export class InfoView {
	/**
	 * Konstruktor
	 */
	constructor () {

		console.log('INFO VIEW');
	}
  
  
	/**
	 * Holt das HTML-Element das die Versionangabe enthaelt.
	 * @returns {HTMLElement}
	 */
 	getVersionElement () {
		return document.querySelector('p.version')
	}
} // InfoView
