/**
 * A P P  
 *  - "Wurzel" des JavaScript MVC (Model View Controller) Musters und 
 *      Registrierung des Service Workers.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 */


'use strict'; // Strikter Modus fuer komplettes Skript


import {Configuration} from './modules/configuration.js';
import {HttpParams} from './modules/models/http/params.js';
import {HttpRestClient} from './modules/models/http/rest/client.js';
import {Utilities} from './modules/utilities.js';
import {View} from './modules/view.js';
import {Router} from './modules/router.js';


/**
 * @note Alle globalen Variablen beginnen mit einem Unterstrich '_', 
 *   werden ausschliesslich in Grossbuchstaben geschrieben und 
 *   werden nur in dieser Datei deklariert. 
 */

/**
 * @typedef {Object} window
 * @property {Object} _ROUTER 
 */
window._ROUTER = null;

/**
 * @typedef {Object} window
 * @property {Object} _DEVEL Objekt und seine Eigenschaften zur Erleichterung 
 *   der Entwicklungsarbeit. Kann ausgebaut werden.
 */
window._DEVEL = {};

/**
 * @typedef {Object} window
 * @property {} Identifikationsnummber des Interval-Timers. Diese globale 
 *   Variable wird vom "Logout-Timer" genutzt.
 */
window._INTERVAL_TIMER = null;


/**
 * Registrierung des Service Worker (sw)
 */
/*
const registerServiceWorker = async () => {
	if ('serviceWorker' in navigator) {
		try {
			const registration = await navigator.serviceWorker.register(
				'./js/sw.js',
				{
					scope: './js/',
					type: 'classic'
				}
			);
			if (registration.installing) {
				console.log('SERVICE WORKER installing');
			} else if (registration.waiting) {
				console.log('SERVICE WORKER installed');
			} else if (registration.active) {
				console.log('SERVICE WORKER active');
			}
		} catch (error) {
			console.error(`SERVICE WORKER registration failed with ${error}`);
		}
	} else {
		console.error("Service workers are not supported.");
	}
};
*/


/**
 * Start bzw. Initialisierung des MVC-Systemmusters. 
 *   Der Router _ROUTER ist global.
 * @returns {void}
 */
async function initMVC() {
    
    var config = await Configuration.getInstance();
    
    var client = new HttpRestClient(config, new HttpParams({}));
    
    var utils = new Utilities(config, client);
    
    var view = new View(config, utils, client);
    
    window._ROUTER = new Router(config, utils, client, view);
    
}


//registerServiceWorker();
initMVC();
