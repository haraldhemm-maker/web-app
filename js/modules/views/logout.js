/** 
 * L O G O U T   V I E W   
 * - Ansicht der Logout-Seite.
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
	constructor () {
   		super();
   		this.setProxyTarget(this).createProxy();
   		this.PAGE_ID = 'logout'; // Initialisierung der (Pseudo-)Konstante.
   		
		console.log('LOGOUT VIEW');
	}
} // LogoutView
