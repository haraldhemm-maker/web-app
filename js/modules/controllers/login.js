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
        
        // Ereignisverwaltung fuer den Anmelde-Knopf.
        this.view.proxy.elementOauth.addEventListener('click', (evt) => {
            evt.preventDefault();
            this.model.startLogin();
        });
        
        // Ereignisverwaltung fuer den Registrieren-Knopf.
        this.view.proxy.elementOauthRegister.addEventListener('click', (evt) => {
            evt.preventDefault();
            this.model.startRegister();
        });
        
        // Pruefen, ob der lokale OAuth2-Server erreichbar ist.
        this.model.checkOauthServer()
        .then((response) => {
            this.view.handlingLoginAndRegisterBtn(response);            
        });
        
	    console.log('LOGIN CONTROLLER');
    }
} // LoginController
