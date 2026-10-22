/** 
 * P R O X Y   V I E W   
 *  - Stellvertreterklasse der lokalen Ansichten.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class ProxyView {
    /**
     * Proxy target (Stellvertreterzielobjekt)
     * @var {Object}
     */
    target = null;
  
    /**
     * Proxy (Stellvertreter)
     * @var {Proxy}
     */
    proxy = null;
    
    /**
     * Proxy handler
     * @var {Object}
     */
    handler = {
        get: function (target, prop) {
            prop = target.transformName(prop);
            if ('element_' === prop.slice(0, 'element_'.length)) {
                prop = prop.slice('element_'.length, prop.length);
                prop = target.PAGE_ID + '_' + prop;
                return window.document.getElementById(prop);
            } 
            else if ('elements_' === prop.slice(0, 'elements_'.length)) {
                prop = prop.slice('elements_'.length, prop.length);
                if ('input_checkbox' === prop) {
                    return window.document.querySelectorAll('input[type="checkbox"]');
                }
                else if ('button' === prop) {
                    return window.document.getElementsByTagName('button');
                }
                else {
                    const items = prop.split('_');
                    if ('label' === items[0]) {
                        let selector = items[0] + '[for^="';
                        for (let i = 1; i < items.length; i++) {
                            selector += items[i] + '_';	
                        }
                        selector += '"]';
                        return window.document.querySelectorAll(selector);
                    }
                }
                return target[prop];
            } 
            else {
                return target[prop];
            }
        }
    };

  
    /**
     * Konstruktor
     */
    constructor () {
        // zur Zeit keine Anweisungen
    }
    
    
    /**
     * Setzt das Stellvertreterzielobjekt.
     * @param {Object} target Stellvertreterzielobjekt
     * @returns {ProxyView}
     */
    setProxyTarget (target) {
        this.target = target;
        return this;
    }
    
    
    /**
     * Erzeugt ein Stellvertreterobjekt
     * @return {ProxyView}
     */
    createProxy () {
        this.proxy = new Proxy(this.target, this.handler);
        return this;
    }
    
    
    /**
     * Wandelt einen Bezeichner (Namen) von der "lower camelCase" in die 
     *   "snake_case" Schreibweise um.
     * @param {String} camelCaseName
     * @returns {String} snake_case_name
     */   
    transformName (camelCaseName) {
        let snakeCaseName = '';
        const nL = camelCaseName.length;
        let i = 0;
        for (; i < nL; i++) {
            if (camelCaseName.charAt(i) === camelCaseName.charAt(i).toUpperCase()) {
                snakeCaseName += '_' + camelCaseName.charAt(i).toLowerCase();
            }
            else {
                snakeCaseName += camelCaseName.charAt(i);
            }
        }
        return snakeCaseName;
    } // transformName  
} // ProxyView
