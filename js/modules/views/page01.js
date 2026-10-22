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
export class Page01View extends ProxyView  {
    /**
     * Identifikationszeichenkette der "Seite".
     * @const {String}
     */
    PAGE_ID = 'page01';

    
	/**
	 * Konstruktor
	 */
	constructor () {
   		super();
   		this.setProxyTarget(this).createProxy();
   		this.PAGE_ID = 'page01'; // Initialisierung der (Pseudo-)Konstante.
   		
		console.log('PAGE01 VIEW');
	}
} // Page01View
