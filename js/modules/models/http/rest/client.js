/**
 * HTTP REST (Representational State Transfer) Client 
 *  - Klient zur Nutzung des HTTP REST Interfaces.
 * 
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class HttpRestClient {
	/**
	 * Konstruktor
	 * 
	 * @param {ConfigurationSingleton} config Instance of the configuration 
	 *   singleton. 
	 * @param {HttpParams} httpParams Parameter des HTTP Requests. 
	 */
	constructor (config, httpParams) {
		/**
		 * Instance of the configuration singleton.
		 * @property {ConfigurationSingleton} config
		 */
		this.config = config;
		/**
		 * HTTP Parameter
		 * @property {httpParams} httpParams
		 */
		this.httpParams = httpParams;
		/**
		 * Version des HTTP REST Clients
		 * @property {String} version Moegliche Werte sind zur Zeit 'v2' 
		 * 	oder 'v3'. Prinzipiell sind folgende Werte moeglich 'v0'...'v9' 
		 * 	sowie 'v10'...'v99'. Die aktuelle Version ist in der Zeichenkette
		 * 	this.config.REST.URL_CTRL kodiert.
		 */
		const regex = /v(0|[1-9][0-9]?)/;
		this.version = this.config.REST.URL_CTRL.match(regex);
	}
    
    
	/**
	 * Setzt die Version des HTTP REST Clients
	 * 
	 * @param {String} version Moegliche Werte sind zur Zeit 'v2' oder 'v3'. 
	 * @return {HttpRestClient} Fluent interface.
	 */
	setVersion (version) {
		this.version = version;
		return this;
	}
    
    
	/**
	 * Aufruf des REST-Kontrollers auf dem Server.
	 *
	 * @param {string} httpResquestMethod Bezeichner der HTTP Request Methode 
	 *   ('DELETE', 'GET', POST', 'PUT').
	 * @returns {Promise}
	 * @version 2.0.0
     */
	async exec (httpRequestMethod) {
		if (window._ROUTER) {
			window._ROUTER.view.showSpinner();
		}
		httpRequestMethod = httpRequestMethod.toUpperCase();
		let url = window.location.origin + this.config.REST.URL_CTRL;
		const regex = /v(0|[1-9][0-9]?)/;
		url.replace(regex, this.version);		
		const headers = new Headers();
		headers.append('Content-Type', 'application/x-www-form-urlencoded');
		const options = {
			headers: headers,
			method: httpRequestMethod
		};
		if ('GET' === httpRequestMethod) { // GET
			url += '?' + this.httpParams.toString();
		}
		else { // // DELETE || POST || PUT
			options.body = this.httpParams.toString();
		}
		const response = await fetch(url, options);
		const responseJson = await response.json();
		if (window._ROUTER) {
			window._ROUTER.view.hideSpinner();
		}
		return responseJson;				
	}
} // HttpRestClient
