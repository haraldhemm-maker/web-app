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
        
        // Die Anfangsrouten festlegen.
        this.routes = this.getInitialRoutes();
        
        // Ereignisverwaltung des Routers.
        if (window) {
          window.addEventListener('load', (this.initRouting).bind(this));
          window.addEventListener('hashchange', (this.handleRouting).bind(this));
        }
    }
    
    
    /**
     * Zerlege eine Query-Zeichenkette (location.search) in ihre Bestandteile.
     * 
     * @global {String} document.location.search
     * @returns {Map} die Bestandteile der Query-Zeichenkette als 
     *  Schluessel/Wert-Paare in einer Map. 
     */
    parseQuery() {
        const params = new URLSearchParams(document.location.search);
        const map = new Map();
        for (const [key, value] of params) {
          map.set(key, value);
        }
        return map;
    }
    
    
    /**
     * Holt die moeglichen Anfangsrouten (die anfaenglichen 
     *   Navigationsmoeglichkeiten). Zur Zeit gibt es nur eine Anfangsroute, 
     *   naemlich die zur Login-Seite (siehe Konfiguration).
     * @returns {Map} Sammlung von Schluessel/Wertpaaren. Die Schluessel sind 
     *   die Identifikationszeichenketten der einzelnen Seiten (Container) und 
     *   die entsprechenden Werte sind Objekte unter anderem mit der Eigenschaft
     *   der Seitennamen (Containernamen) wie diese im Menue erscheinen sollen. 
     *   Z.B.: {'login', Object:{menu_name: 'Anmelden', menu_sort: 1, ...}}
     */
    getInitialRoutes() {
        const routes = new Map();        
        const objPages = this.config.CLIENT.PAGES;
        for (const prop in objPages) {
            if (this.utils.isString(objPages[prop])
            && this.utils.isValidJson(objPages[prop])) {
                routes.set(prop, JSON.parse(objPages[prop]));
            }
        }
        return routes;
    }
    
    
    /**
     * Initialisiert das Routing. Hier wird beim Eintreffen des 
     *   'load'-Ereignisses das Routing fuer das Ereignis 'hashchange' 
     *   ausgefuehrt.
     * 
     * @param {Event} evt 'load'-Ereignis
     * @return {void}
     */
    initRouting(evt) {
        this.handleRouting(evt);
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
        
        //if (this.isUserLoggedIn()) {
        if (window._LOGIN) {
            // The location.hash property sets or returns the anchor part 
            //  of a URL, including the hash sign (#).
            const currentPageId = location.hash.slice(1); // without hash sign.
            newPageId = (0 != currentPageId.length)? currentPageId: this.config.default_page_id;
            await this.getRoutes().then(async (response) => {
                this.routes = await response;
                this.view.createMenu(this.routes);
            });
        }
        else {
            this.routes = this.getInitialRoutes();
            this.view.createMenu(this.routes);
        }
        /*
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
                const specificView = new (eval(viewName)))(this.config, this.utils, this.client);
                const specificController = new (eval(controllerName))(specificModel, specificView, this.config);
            })
            .catch(error => {
                console.log(error);
            });
        }
        */
        return this;
    }
} // Router
