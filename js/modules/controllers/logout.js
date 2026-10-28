/**
 * L O G O U T   C O N T R O L L E R   
 *  - Steuerung der Logout-Seite.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class LogoutController {
    /**
     * Konstruktor
     * @param {LogoutModel} model
     * @param {LogoutView} view
     * @param {Configuration} config
     */
    constructor (model, view, config) {
        this.model = model;
        this.view = view;
        this.config = config;
        
        // Abspielen des Schulgongs starten.
        this.view.startPlayingSchoolBell();
	
        // Ereignisverwaltung des "Abbrechen" ("logout_cancel")-Knopfes.
        this.view.getCancelButton().addEventListener('click', (evt) => {
            evt.stopPropagation();
            this.view.stopPlayingSchoolBell();
            clearInterval(window._INTERVAL_TIMER);
            window._INTERVAL_TIMER = null;
            location.replace('#info');
        });
        
        // Zeitgeber des 
        //  Abmeldeprozesses starten und vom AS (Authentification-Server) 
        //  abmelden.
            
        let reverseCounter = 10;
        window._INTERVAL_TIMER = setInterval(
            function(){
                this.view.setProgressBar(10 - --reverseCounter);
                if (0 >= reverseCounter) {
                    clearInterval(window._INTERVAL_TIMER);
                    window._INTERVAL_TIMER = null;
                    this.model.logout()
                    .then((response) => { console.log(response); //@debug HaHeLa [2026-10-28]
                        if (response.result) {
                            // Umleitung auf eine neue Seite ist erforderliche, 
                            //  damit das Ereignis 'hashchange' ausgeloest und
                            //  damit das oeffentliche Menue angezeigt wird.
                            //@note HaHeLa Die Methode replace ist erforderlich.
                            location.replace('frontend.html'); 
                        }
                    });
                }
                this.view.setCounting(reverseCounter);
            }.bind(this), 1000
        );
    	    
	    console.log('LOGOUT CONTROLLER');
    }
} // LogoutController
