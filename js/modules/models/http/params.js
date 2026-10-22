/**
 * HTTP Parameter  - Umgang mit den HTTP Parametern.
 * 
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class HttpParams {
    constructor (options) {
        /**
         * Optionen
         * @var Object
         */
        this.options = options;
        
        /**
         * Alle Parameter in einem Feld organisiert.
         * @var Array
         */
        this.params = new Array();
    }
    
    
    /**
     * Prueft, ob der Parameter bereits gespeichert ist.
     * 
     * @param {String} name Der Bezeichner (Name) des Parameters.
     * @returns {Boolean} Die Methode liefert den Wert true, falls der 
     *   Parameter bereits gespeichert wurde. Sonst liefert die Methode 
     *   den Wert false.
     */
    has (name) {
        for (let i = 0; i < this.params.length; i++) {
            if (name == this.params[i].name) {
                return true;
            }
        }
        return false;    
    }
    
    
    /**
     * Speichere einen Parameter.
     *   Ist der Parameter noch nicht gespeichert, so wird sein Name und sein 
     *   Wert gespeichert. Ist der Parameter bereits gespeichert (vorhanden), 
     *   dann wird nur sein Wert aktualisiert und gespeichert.
     * 
     * @param {String} name Der Bezeichner (Name) des Parameters.
     * @param {String} value Der Wert des Parameters.
     * @returns {HttpParams} "Fluent Interface"
     */
    set (name, value) {
        for (let i = 0; i < this.params.length; i++) {
            if (name == this.params[i].name) {
                this.params[i].value = value;
                return this;
            }
        }
        let param = {};
        param.name = name;
        param.value = value;
        this.params.push(param);
        return this;
    }
    
    
    /**
     * Hole den Wert eines Parameters.
     *   Ist der Parameter nicht vorhanden, dann wird der Wert null 
     *   zurueck gegeben.
     * 
     * @param {String} name Der Bezeichner (Name) des Parameters.
     * @returns {String|Number|null} Der Wert des Parameters.
     */
    get (name) {
        for (let i = 0; i < this.params.length; i++) {
            if (name == this.params[i].name) {
                return this.params[i].value;
            }
        }
        return null;
    }
    
    
    /**
     * Loescht einen Parameter.
     * 
     * @param {String} name Der Bezeichner (Name) des Parameters.
     * @returns {HttpParams} "Fluent Interface" 
     */
    delete (name) {
        for (let i = 0; i < this.params.length; i++) {
            if (name == this.params[i].name) {
                this.params.splice(i,1);
                return this;
            }
        }
        return this;
    }
    
    
    /**
     * Loescht alle Parameter.
     *
     * @returns {HttpParams} "Fluent Interface" 
     */
    clear () {
        this.params = new Array
        return this;
    }
    
      
    /**
     * Die Parameter werden zu einer "URL-codierten" Zeichenkette 
     *   "serialisiert".
     * 
     * @returns {String} "URL-codierte" Zeichenkette der serialisierten 
     *   Parameter, oder, falls keine Parameter vorhanden waren, eine leere
     *   Zeichenkette.
     */
    toString () {
        let paramsStr = '';
        for (let i = 0; i < this.params.length; i++) {
            if (0 < paramsStr.length) {
                paramsStr += '&';
            }
            paramsStr += encodeURIComponent(this.params[i].name)+'='+encodeURIComponent(this.params[i].value);
        }
        return paramsStr;
    }
} // HttpParams
