/** 
 * V I E W   
 *  - Globale Praesentation.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */


export class View {
    /**
     * Konstruktor
     *
     * @param {ConfigurationSingleton} config Instance of the configuration 
     *   singleton. 
     * @param {Utilities} utils Instance of the utilities singleton.
     * @param {HttpRestClient} client Klient zur Nutzung des 
     *   HTTP REST Interfaces.
     */
    constructor (config, utils, client) {
        this.config = config;
        this.utils = utils;
        this.client = client;
        
        this.fillFooter();    
    }
    
    
    /**
     * Holt die Identifikationszeichenkette der aktuellen Seite 
     *   (des aktuellen Containers).
     *
     * @returns {String} Identifikationszeichenkette der aktuellen Seite, 
     *   z.B. 'info'.
     */
    getCurrentPageId () {
        const divElement = document.querySelector('main+div.container');
        return (null != divElement)? divElement.id: '';
    }
  
  
    /**
     * Klappt das Menue zu.
     *
     * @returns {View} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle").
     */
    closeMenu () {
        const element = document.getElementById('navbarCollapse');
        element.classList.remove('show');
        return this;
    }
    
    
    /**
     * Fuellt den Fussbereich mit einheitlichem Inhalt, wie Copyright.
     *
     * @returns {View} Gibt die Klasse selbst zurueck (fluent interface).
     */ 
    fillFooter () {
        const date = new Date();
        const currentYear = date.getFullYear();
        let copyright = 'Copyright &copy; '+ this.config.INFO.YOM;
        if (currentYear > this.config.INFO.YOM) {
            copyright += ' &ndash; ' + currentYear;
        }
        copyright += ' <abbr title="Harald Hemm Landstuhl">HaHeLa</abbr>';
        document.querySelector('footer>div.container>span.copyright').innerHTML = copyright;
        return this;
    }
    
    
    /**
     * Holt ein HTML-Schnipsel.
     *
     * @param {DOMString} uri Einheitlicher Bezeichner der HTML-Ressource, 
     *   z.B. this.config.base.path + 'htm/page/login.inc.htm'; 
     * @returns {Promise}
     */
    getHTML (uri) {
        const url = window.location.origin + uri;
        return new Promise(function (resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function() {
                if (4 == this.readyState) {
                    if (200 == this.status) {
                        resolve(xhr.response);
                    } 
                    else {
                        const error = new Error(
                            'HaHeLa - Fehler beim XMLHttpRequest: readyState = ' + this.readyState + ', status = ' + this.status
                        );
                        reject(error);
                    }
                }
            };
            xhr.send();
        });
    }
    
    
    /**
     * Erzeugt das Navigationsmenue. 
     *
     * @param {Map} Sammlung von Schluessel/Wertpaaren verfuegbarer Routen.
     * @returns {View} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle").
     * @note Auch wenn ein Eintrag im Navigationsmenue vorhanden ist, so muss 
     *   dieser nicht zwangslaeufig angezeigt werden. Wie bei dem Menueeintrag 
     *   'Anmelden' ist es moeglich, dass die CSS-Eigenschaften des 
     *   entsprechenden HTML-Element die Darstellung verhindern.
     */
    createMenu (routes) {
        this.emptyMenu()
        .buildMainMenu(routes)
        .buildSubMenus(routes);
        return this;
    }
	
	
    /**
     * Baut die Hauptnavigation auf (Navigationslevel 0). 
     *
     * @param {Map} Sammlung von Schluessel/Wertpaaren verfuegbarer Routen.
     * @returns {View} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle").
     * @note Auch wenn ein Eintrag im Navigationsmenue vorhanden ist, so muss 
     *  dieser nicht zwangslaeufig angezeigt werden. Ob ein Eintrag angezeigt 
     *  wird, ist auch von seiner die CSS-Eigenschaften abhaengig. 
     */
    buildMainMenu (routes) { 
        const ulElement = document.querySelector('nav ul');
        routes.forEach((value, key) => { //console.log(value, key); // @debug HaHeLa [2025-09-03]
            // value => Object {menu-display: "", menu_name: "Info", menu_sort: 1 }
            // key => 'info'
  		    if ('none' !== value.menu_display && -1 === key.indexOf('-')) {
                // Verlinkung (Anker-Element) und Listen-Element aufbauen: 
                const aNode = document.createElement('A');
                const liNode = document.createElement('LI');
                if (this.hasSubMenu(routes, key)) {
                    aNode.setAttribute('aria-expanded', 'false');
                    aNode.classList.add('nav-link', 'dropdown-toggle');
                    aNode.setAttribute('data-bs-toggle', 'dropdown');
                    aNode.setAttribute('href', '#');
                    aNode.setAttribute('id', key);
                    aNode.setAttribute('role', 'button');
                    liNode.classList.add('nav-item', 'dropdown');
                }
                else {
                    aNode.setAttribute('class', 'nav-link');
                    aNode.setAttribute('href', '#'+key);
                    liNode.setAttribute('class', 'nav-item');
                }
                aNode.text = value.menu_name;
                // Anker-Element dem Listen-Element hinzufuegen.
                liNode.appendChild(aNode);
                //liNode.addEventListener('click', this.closeMenu);
                // Listen-Element dem Navigation-Element hinzufuegen.
                ulElement.appendChild(liNode);
  		    }
        });	
        return this;
    }
    
	
    /**
     * Baut die Untermenues bei Bedarf auf.
     * 
     * @param {Map} routes Sammlung von Schluessel/Wertpaaren verfuegbarer Routen.
     * @returns {View} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle").
     */
    buildSubMenus (routes) {
        routes.forEach((value, key) => { 
            /*
            if ('none' !== value.menu_display && -1 < key.indexOf('-')) {
                let roleValue = window._ACCOUNT_ROLES.get(window._ACCOUNT.role);
                let menuDisplayValue = window._ACCOUNT_ROLES.get(value.menu_display);
                if (roleValue >= menuDisplayValue) {        
                    const entries = key.split('-');
                    // Zuerst einige Aenderungen am Hauptmenue vornehmen.
                    const aElement = document.querySelector('nav ul li a[href="#' + entries[0] + '"]');
                    if (null !== aElement) {
                        const liElement = aElement.parentElement;
                        if (!liElement.classList.contains('dropdown')) {
                            liElement.classList.add('dropdown');
                            aElement.classList.add('dropdown-toggle');
                            aElement.setAttribute('data-bs-toggle', 'dropdown');
                            aElement.setAttribute('aria-expanded', 'false');
                            const newUlElement = document.createElement('ul');
                            newUlElement.setAttribute('class', 'dropdown-menu');
                            newUlElement.setAttribute('data-bs-popper', 'static');
                            liElement.appendChild(newUlElement);
                        }
                        // Erst jetzt das Untermenue aufbauen.
                        // neues Anker-Element
                        const newAElement = document.createElement('a');
                        newAElement.setAttribute('class', 'dropdown-item');
                        newAElement.setAttribute('href', '#'+key);
                        newAElement.text = value.menu_name;
                        // neues Listen-Element 
                        const newLiElement = document.createElement('li');
                        // neues Anker-Element dem neuen Listen-Element hinzufuegen.
                        newLiElement.appendChild(newAElement);
                        // neues Listen-Element der neuen Liste hinzufuegen.
                        liElement.querySelector('ul').appendChild(newLiElement);
                    }
                }
            }
            */
        });
        return this;
    }
    
    
    /**
     * Ueberprueft ob ein Menuepunkt, identifiziert mit Hilfe der 
     * 	Identifikationszeichenkette (id), Untermenuepukte hat. 
     * 
     * @param {Map} routes Sammlung von Schluessel/Wertpaaren verfuegbarer 
     * 	Routen.
     * @param {Sting} id Identifikationszeichenkette eines Menuepunktes fuer 
     * 	den geprueft werden soll. ob dieser Untermenuepunkte hat.
     * @return {Boolean} Wenn der Menuepunkt mindestens einen Untermenuepunkt 
     * 	hat, dann liefert die Methode den boolsceh Wert true, sonnst false.
     */
    hasSubMenu (routes, id) {
        id += '-';
        routes.forEach((value, key) => {
            if (key.startsWith(id))
                return true;
        });
        return false;
    }
    
    
    /**
     * Spinner/Loader anzeigen.
     *
     * @returns {View} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle").
     */
    showSpinner () {
        document.getElementsByClassName('spinner-grow')[0]
        .style.visibility = 'visible';
        return this;
    }
    
    
    /**
     * Spinner/Loader verstecken.
     *
     * @returns {View} Gibt das Objekt selbst zurueck 
     *   ("sprechende Schnittstelle").
     */
    hideSpinner () {
        document.getElementsByClassName('spinner-grow')[0]
        .style.visibility = 'hidden';
        return this;
    }
    
    
    /**
     * Leert das Navigationsmenue.
     *
     * @returns {View} Gibt das Objekt selbst zurueck 
     *  ("sprechende Schnittstelle").
     */
    emptyMenu () {
        const ulElement = document.querySelector('nav ul');
        // Menueeintraege loeschen.
        while (ulElement.hasChildNodes()) {
            ulElement.removeChild(ulElement.lastChild);
        }
        return this;
    }
} // View
