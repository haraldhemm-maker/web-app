/** 
 * P R O F I L E   ( U S E R )   M O D E L   
 * - Modell der Profileseite des Benutzers.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class ProfileModel {
    /**
     * Konstruktor
     * @param {Configuration} config Instanz des Konfiguration-Singleton. 
     * @param {Utilities} utils Instanz des Werkzeug-Singletons.
     * @param {HttpRestClient} client Instanz des HTTP-REST-Klienten.

     */
    constructor (config, utils, client) {
	    this.config = config;
	    this.utils = utils;
        this.client = client;
	  
	    console.log('PROFILE MODEL');
    }
    
    
    /**
     * Holt die Profildaten es aktuellen Benutzers.
     * 
     * @returns {Promise} {Object} response.info Profildaten
     */
    getProfile() {
        this.client.httpParams.clear()
        .set('cmd', 'User')
        .set('act', 'info');
        return this.client.setVersion('v1').exec('GET');
    }
    
    
    /**
     * Speichert die Daten des Benutzerkontos in die lokale Datenbank.
     * 
     * @param {FormData} formData Zur Zeit sind folgende Daten im 
     *  formDate-Objekt enthalten:
     *      {Number} profile_id
     *      {Number} profile_sub
     *      {String} profile_role
     *      {String} profile_username
     *      {String} profile_first_name
     *      {String} profile_last_name
     * @return {Promise} 
     */
    saveUserData(formData) {
        this.client.httpParams.clear()
        .set('cmd', 'User')
        .set('act', 'save');
        // Ueber alle Eintraege von formData iterieren.
        for (const [key, value] of formData.entries()) {
            this.client.httpParams.set(key.replace('profile_', ''), value);
        }
        let requestMethod = '';
        if (this.utils.isEmpty(formData.get('profile_id'))) 
            requestMethod = 'POST'; // Create new DB-Data Record
        else 
            requestMethod = 'PUT'; // Update existing DB-Database Record
        return this.client.setVersion('v1').exec(requestMethod);
    }
} // ProfileModel
