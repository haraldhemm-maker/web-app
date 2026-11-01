/**
 * P R O F I L E   ( U S E R )   C O N T R O L L E R   
 * - Steuerung des Benutzerprofils.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class ProfileController {
    /**
     * Konstruktor
     * @param {ProfileModel} model
     * @param {ProfileView} view
     * @param {Configuration} config
     */
    constructor (model, view, config) {
	    this.model = model;
	    this.view = view;
	    this.config = config;
        
        this.view.hideElement(this.view.proxy.elementAlertInfo);
        
        this.model.getProfile()
        .then(async (response) => { //console.log(response); //@debug HaHeLa [2026-11-01]
            // Profilbild
            const src = './img/user/' + response.info.sub + '.png';
            const ok = await this.view.imgExists(src);
            if (ok) {
                this.view.getProfileImgElement().src = src;
            } else {
                // z. B. Fallback-Bild setzen
                this.view.getProfileImgElement().src = './img/user/0.png';
            }
            // Profildaten vom OAuth2-Server
            this.view.proxy.elementSub.value = response.info.sub;
            this.view.proxy.elementRole.value = response.info.role;
            this.view.proxy.elementUsername.value = response.info.username;
            // Profildaten vom lokalen Webserver/Datenbank
            this.view.proxy.elementId.value = response.info.id;
            this.view.proxy.elementFirstName.value = response.info.first_name;
            this.view.proxy.elementLastName.value = response.info.last_name;
        });
        
        // Ereignisverwaltung fuer den "Speichern"-Knopf
        this.view.proxy.elementSaveBtn.addEventListener('click', (evt) => {
            evt.stopPropagation();
            const formData = new FormData(this.view.proxy.elementForm);
            this.model.saveUserData(formData)
            .then((response) => { //console.log(response); //@debug HaHeLa [2026-11-01]
                const alertInfoElem = this.view.proxy.elementAlertInfo;
                if ((typeof 'undefined' !== response.errCode) && (0 < response.errCode)) {
                    alertInfoElem.textContent = response.errMessage;
                }
                else {
                    if ((true === response.result) && ('POST (create)' === response.requestMethod))
                        alertInfoElem.textContent = 'Profil wurde angelegt';
                    else if ((true === response.result) && ('PUT (update)' === response.requestMethod))
                        alertInfoElem.textContent = 'Profil wurde erneuert';
                    else 
                        alertInfoElem.textContent = 'Beim Anlegen oder Erneuern des Profils ist ei Fehler aufgetreten.';
                }
                this.view.showElement(alertInfoElem);
            });
        });
        
	    console.log('PROFILE CONTROLLER');
    }
} // ProfileController
