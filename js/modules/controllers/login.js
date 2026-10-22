/**
 * L O G I N   C O N T R O L L E R   
 * - Steuerung der Login-Seite.
 * 
 * Erweiterung: Beim Laden wird geprueft, ob der OAuth2-Server erreichbar ist
 * Ist er offline, wird der "anmelden"-Link deaktiviert und farblich markiert.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class LoginController {
    /**
     * Konstruktor
     * @param {LoginModel} model
     * @param {LoginView} view
     * @param {Configuration} config
     */
    constructor (model, view, config) {
	    this.model = model;
	    this.view = view;
	    this.config = config;
        
        // Ereignisverwaltung fuer den Anmelde-Link (Initialisierung)
        const link = this.view.proxy.elementOauth;
        link.addEventListener('click', (evt) => {
            evt.preventDefault();
            this.model.startLogin();
        });
        
        // Initialisierung des Anmelde-Links.
        this.view.handlingLoginLink(this.model.checkOauthServer());
        
	    console.log('LOGIN CONTROLLER');
    }
} // LoginController
