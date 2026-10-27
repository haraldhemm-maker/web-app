/**
 * R O U T E R  
 *  - Einfaches URL Routing.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * @description Aufbau des URI siehe RFC 3986 URI Generic Syntax January 2005
 *   https://www.rfc-editor.org/rfc/rfc3986#section-4.1
 *   relative-ref = relative-part [ "?" query ] [ "#" fragment ]
 */

// models Mvc
import {InfoModel} from './models/info.js';
import {LoginModel} from './models/login.js';
import {LogoutModel} from './models/logout.js';
import {Page01Model} from './models/page01.js';
// views mVc
import {InfoView} from './views/info.js';
import {LoginView} from './views/login.js';
import {LogoutView} from './views/logout.js';
import {Page01View} from './views/page01.js';
// controllers mvC
import {InfoController} from './controllers/info.js';
import {LoginController} from './controllers/login.js';
import {LogoutController} from './controllers/logout.js';
import {Page01Controller} from './controllers/page01.js';


export class Router {
    /**
     * Konstruktor
     * @param {Configuration} config Instanz des Konfiguration-Singleton. 
     * @param {Utilities} utils Instanz des Werkzeug-Singletons.
     * @param {HttpRestClient} client Klient zur Nutzung des 
     *   HTTP REST Interfaces.
     * @param {View} view Instanz der globalen Ansicht.
     */
    constructor(config, utils, client, view) {
        this.config = config;
        this.utils = utils;
        this.client = client;
        this.view = view;
        
        this.handleRouting(new HashChangeEvent('hashchange', {}));        
        if ('undefined' !== typeof window) {
            window.addEventListener('hashchange', this.handleRouting.bind(this));
        }
    }
    
    
    /**
     * Ermittelt die moeglichen Routen (Navigationsmoeglichkeiten) zu 
     *   vorhandenen Seiten (Container) und gibt diese als eine Sammlung 
     *   von Schluessel/Wert-Paaren (Map) zurueck. Die Schluessel sind die 
     *   Identifikationszeichenketten der einzelnen Seiten (Container) und 
     *   die entsprechenden Werte sind Objekte unter anderem mit den Namen 
     *   der Seiten (Container) wie diese im Menue erscheinen sollen.
     * @returns {Map} Sammlung von Schluessel/Wertpaaren moeglicher Routen.  
     * @note Fuer die Nutzung dieser Methode NUSS der Benutzer angemeldet sein.
     */
    getRoutes() {
        this.client.httpParams.clear()
        .set('cmd', 'System')
        .set('act', 'get_menu_map');
        return this.client.exec('GET')
        .then(response => { // Kein "interner" Fehler
            if ('undefined' == typeof response.errCode || 0 == response.errCode) {
                let responseMap = response.map;
                responseMap = Object.entries(responseMap);
                return new Map(responseMap);
            }
            else { // Fehler bei der REST API "System" Fehler
                console.log(response);
                return new Map();
            }
        }).catch(error => { // "Interner" Fehler beim XMLHttpRequest.
            console.error(error.message);
            return new Map();
        });
    }
    
    
    /**
     * Holt den Uniform Resource Identifier (URI) einer HTML-Seite.
     * 
     * @param {String} pageId Identifikationszeichenkette einer Seite.
     * @return {String} URI Uniform Resource Identifier einer Seite.
     */
    getPageUri(pageId) {
        let uri = this.config.CLIENT.BASE_PATH + 'htm/pages/';
        const parts = pageId.split('-');
        let firstPass = true;
        for (let i = 0; i < parts.length; i++) {
            if (firstPass) {
                uri += parts[i];
                firstPass = false;
            }
            else uri += '/' + parts[i];
        }
        uri += '.inc.htm';
        return uri;
    }
    
    
    /**
     * Behandelt das Routing.
     * 
     * @param {Event} evt 'load' oder ein 'hashchange'-Ereignis.
     * @returns {Router} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle“).
     */
    async handleRouting(evt) {
        evt.stopPropagation();
        this.view.closeMenu();
        // The location.hash property sets or returns the anchor part 
        //  of a URL, including the hash sign (#).
        const currentPageId = location.hash.slice(1); // without hash sign.
        const newPageId = (0 != currentPageId.length)? 
            currentPageId: this.config.CLIENT.DEFAULT_PAGE_ID;
        await this.getRoutes().then(async (response) => {
            this.routes = response;
            this.view.createMenu(this.routes);
        });
        // When location.hash is used to set the anchor part, 
        //  do not include the hash sign (#).
        location.hash = newPageId;
        // Seite laden und anzeigen.
        if (this.view.getCurrentPageId() != newPageId) {
            const uri = this.getPageUri(newPageId);
            this.view.getHTML(uri)
            .then(response => {
                // Seite laden
                const element = document.getElementsByTagName('main')[0];
                element.innerHTML = response;
                // Speziefischer Controller und spezifische View sowie 
                //  spezifisches Modell der ausgewaehlten Seite.
                const modelName =  this.utils.camelizePageId(newPageId) + 'Model';
                const viewName = this.utils.camelizePageId(newPageId) + 'View';
                const controllerName = this.utils.camelizePageId(newPageId) + 'Controller';
                const specificModel = new (eval(modelName))(this.config, this.utils, this.client);
                const specificView = new (eval(viewName))(this.config, this.utils, this.client);
                const specificController = new (eval(controllerName))(specificModel, specificView, this.config);
            })
            .catch(error => {
                console.log(error);
            });
        }
        return this;
    }
} // Router
