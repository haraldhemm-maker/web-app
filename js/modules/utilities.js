/**
 * U T I L I T I E S  
 *  - Allgemeine, nuetzliche Werkzeuge.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * @class
 */
export class Utilities {
	/**
	 * Constructor (Singleton)
	 *
	 * @param {Configuration} config Konfiguration Singleton.
	 * @param {HttpRestClient} client Klient zur Nutzung des 
	 *   HTTP REST Interfaces.
	 */
	constructor(config, client) {
		this.config = config;
		this.client = client;

		if (!Utilities.instance) {
			Utilities.instance = this;
		}
		return Utilities.instance;
	}
    
    
    /**
     * Ueberprueft, ob der uebergebene Parameter eine Zeichenkette ist oder 
     *  nicht.
     * 
     * @param {*} param Parameter mit beliebigen Typ.
     * @returns {Boolean} Wenn der uebergebenen Parameter eine Zeichenkette 
     *  ist, dann liefert die Methode den Wert true, sonst false. 
     */
    isString(param) {
        return (typeof param === 'string' || param instanceof String)? 
            true: false;
    }

    
    /**
     * Ueberprueft, ob der Inhalt eines String einer gueltigen JSON-Codierung
     *  entspricht.
     * 
     * @param {String} str 
     * @returs {Boolean} Wenn der Inhalt des String einer gueltigen 
     *  JSON-Codierung entspricht, dann liefert die Methode den Wert true, 
     *  sonst false. 
     */
    isValidJson(str) {
        try {
            JSON.parse(str);
            return true;
        } catch (e) {
            return false;
        }
    }


	/**
	 * Camelisiere die Seitenidentifizierungszeichenkette, z.B. 
	 *   'security' wird zu 'Security' oder 
	 *   'security-management' wird zu 'SecurityManagement'.
	 * 
	 * @param {String} pageId Seitenidentifizierungszeichenkette
	 *   z.B. 'security-management'.
	 * @returns {String} Camelisierte Seitenidentifizierungszeichenkette
	 *   z.B. 'SecurityManagement'.
	 */
	camelizePageId(pageId) {
		let camel = '';
		const parts = pageId.split('-');
		for (let i = 0; i < parts.length; i++) {
			camel += this.capFLW(parts[i]);
		}
		return camel;
	}
    
    
	/**
	 * Capitalizes the first letter of the word and leaves the rest of the 
	 *   word unchanged.
	 *
	 * @param {String} word
	 * @returns {String}
	 */
	capFLW(word) {
		return word[0].toUpperCase() + word.slice(1);
	}
    
    
	/**
	 * Makes the first letter of the text uppercase and the rest lowercase.
	 *
	 * @param {String} text
	 * @returns {String}
	 */
	capitalize(text) {
		return text.charAt(0).toUpperCase() + text.substring(1).toLowerCase();
	}
	
	
	/**
	 * The trim() function removes whitespace from both sides of a text.
	 *
	 * @param {String} text
	 * @returns {String}
	 */
	trim(text) {
		return text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	}
    
    
	/**
	 * Prueft, ob ein Cookie existiert.
	 * 
	 * @param {String} cname Name des Cookies, dessen Existenz geprueft 
	 *  werden soll.
	 * @returns {Boolean} Die Methode liefert den Wert true, wenn der Cookie 
	 *  existiert, sonst false.
	 */
	existsCookie(cname) {
		const regEx = new RegExp("^(?:.*;)?\\s*" + cname + "\\s*=\\s*([^;]+)(?:.*)?$");
		return (null !== document.cookie.match(regEx))? true: false;
	}
    
    
	/**
	 * Setzt ein Cookie.
	 *
	 * @param {String} cname Name des Cookies. Der Name muss URI kodiert 
	 *   vorliegen
	 * @param {String} cvalue Wert des Cookies. 
	 * @param {Number} exdays Anzahl der Tage nachdem das Cookie verfaellt.
	 * @returns {Utilities} Gibt das Objekt selbst zurueck 
	 *   ("sprechende Schnittstelle").
	 */
	setCookie(cname, cvalue, exdays) {
		let d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		let expires = 'expires=' + d.toUTCString();
		let sameSite = 'SameSite=Strict; Secure';
		document.cookie = cname + '=' + cvalue + ';' + expires + ';' + sameSite +';path=/';
		return this;
	}
    
    
	/**
	 * Holt ein Cookie.
	 *
	 * @param {String} cname Name des Cookies.
	 * @returns {String} Cookie-Wert.
	 */
	getCookie(cname) {
		let name = cname + '=';
		let ca = document.cookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return '';
	}
    
    
	/**
	 * Loescht ein Cookie. Das bedeutet, dass das Verfallsdatum des Cookies 
	 *   auf einen Zeitpunkt in der Vergangenheit gesetzt wird. Hier genau 
	 *   ein Tag in der Vergangenheit.
	 *
	 * @param {String} cname Name des Cookies.
	 * @returns {Utilities} Gibt das Objekt selbst zurueck 
	 *   ("sprechende Schnittstelle").
	 */
	deleteCookie(cname) {
		let d = new Date();
		d.setTime(d.getTime() - (1 * 24 * 60 * 60 * 1000));
		let expires = 'expires=' + d.toUTCString();
		let sameSite = 'sameSite=Strict';
		document.cookie = cname + '=;' + expires + ';' + sameSite + ';path=/';
		return this;
	}
    
    
	/**
	 * Prueft, ob eine Variable leer ist. Dies schliesst einen undefinierten 
	 *   Wert mit ein.
	 *
	 * @param {} value
	 * @returns {Boolean} 
	 */
	isEmpty(value) {
		return (value == null || value.length === 0);
	}
    
    
	/**
	 * Holt das aktuelle Datum im ISO-8601-Format (YYYY-MM-DD).
	 * 
	 * @returns {String} Aktuelle Datum im ISO-8601-Format (YYYY-MM-DD).
	 */
	getCurrentDate() {
		const currentDate = new Date();
		return currentDate.toISOString().substring(0, 10);
	}
    
    
	/**
	 * Hole um Tage zurueckliegendes Datum.
	 * 
	 * @param {Number} days Anzahl der zurueckliegenden Tage.
	 * @param {String} Um Tage zurueckliegendes Datum im Format YYYY-MM-DD.
	 */
	getDateDaysBack(days) {
		const prevDate = new Date(this.getCurrentDate());
		prevDate.setDate(prevDate.getDate() - days);
		return prevDate.toISOString().substring(0, 10);
	}
	
	
	/**
	 * Holt das Datum von Gestern im ISO-8601-Format (YYYY-MM-DD).
	 * 
	 * @returns {String} Datum von Gestern im ISO-8601-Format (YYYY-MM-DD).
	 */
	getYesterdaysDate() {
		const yesterday = new Date(this.getCurrentDate());
		yesterday.setDate(yesterday.getDate() - 1);
		return yesterday.toISOString().substring(0, 10);
	}
	
	
	/**
	 * Holt den Wochentagsname.
	 * 
	 * @param {String} isoDate Datum im ISO-8601-Format (YYYY-MM-DD).
	 * @param {String} locale Kuerzel der Lokalitaet z.B. 'de-DE'.
	 * @return Vollstaendiger (ausfuehrlicher) Wochentagsname, 
	 *   z.B. 'Montag'.
	 */
	getDayName(isoDate, locale) {
		const date = new Date(isoDate);
		return date.toLocaleDateString(locale, {weekday:'long'});        
	}
    
    
	/**
	 * Holt vom gegebenen Datum aus, das Datum des ersten zurueckliegenden 
	 *   Werktags (Montag | Dienstat | Mittwoch | Donnerstag | Freitag).
	 *
	 * @param {String} date Datum im ISO-8601-Format (YYYY-MM-DD) von dem 
	 *   aus das Datum des ersten zurueckliegende Werktags ermittelt wird.
	 * @returns {String} Datum im ISO-8601-Format des ersten 
	 *   zurueckliegenden Werktag.
	 * @internal HaHeLa [2023-10-08] Das ermittelte Datum für den Werktag 
	 *   (Montag, ..., Freitag) kann auf einen Feiertag fallen.
	 */
	getPreviousWorkingDayDate(date) {
		const startDate = new Date(date);
		const dayName = this.getDayName(startDate.toISOString().substring(0, 10), 'de-DE');
		const calcDate = new Date(startDate);
		if ('Sonntag' === dayName)
			calcDate.setDate(calcDate.getDate() - 2);
		else if ('Samstag' === dayName)
			calcDate.setDate(calcDate.getDate() - 1);
		return calcDate.toISOString().substring(0, 10);
	}
    
    
	/**
	 * Erstellt aus einem ISO-Datum der Form 'YYYY-MM-DD' das lokale, deutsche 
	 * 	Datum der Form '(D)D,(M)M.YYYY' oder 'NN., (D)D.(M)M.YYYY'.
	 *  NN zweibuchstabenabkuerzung des Wochentagenamens.
	 *
	 * @param {String} dateStr Datum im folgenden ISO-Format:
	 * 	'YYYYY' vierstellige Jahreszahl 
	 * 		z.B. 2025
	 * 	'MM' zweistellige Monatszahl evtl. mit fuehrender Null 
	 * 		z.B. 09 fuer September
	 * 	'DD' zweistellige Tageszahl evtl. mit fuehrender Null
	 *		z.B. 08 fuer Sonntag den 7. September 2025
	 * @param {Boolean} withDayName OPTIONAL Wenn dieser Parameter den Wert true 
	 * 	hat, dann wird eine Zweibuchstabenabkuerzung des Wochentagnamens vor das 
	 * 	lokale Datum gesetzt. Ist der Parameter nicht abgegeben, oder hat der 
	 * 	Wert des Parameters den Wert false, dann wird keine Abkuerzung des 
	 * 	Wochentagnamens vorangestellt. 
	 * @return {String} Lokales, deutsches Datum der Form '(D)D,(M)M.YYYY' oder 
	 * 	'NN., (D)D.(M)M.YYYY':
	 * 	'NN' Zweibuchstabenabkuerzung des Wochentagnamens
	 * 		z.B. 'Mo'| 'Di'| 'Mi'| 'Do'| 'Fr'| 'Sa'| 'So'
	 * 	'YYYYY' vierstellige Jahreszahl 
	 * 		z.B. 2025
	 * 	'(M)M' ein- bzw. zweistellige Monatszahl 
	 * 		z.B. 9 fuer September oder 10 fuer Oktober
	 * 	'(D)D' ein- bzw. zweistellige Tageszahl
	 *		z.B. 8 fuer Sonntag den 7. September 2025 
	 * 		oder 13 fuer Montag den 13. Oktober 2025
	 */
	getLocalDate(dateStr, withDayName = false) {
		const options = {
			year: 'numeric',
			month: 'numeric',
			day: 'numeric'
		};
		if (withDayName) 
			options.weekday = 'short';
		const date = new Date(dateStr);
		return date.toLocaleString('de-DE', options);
	}
    
    
	/**
	 * Eine Dezimalzahl im ueblichen (englischen) Format auf eine, mit zwei 
	 * 	Nachkommastellen gerundete, Währungsangabe umformen.
	 *
	 * @param {String} currencyStr Eine Währungsangabe im ueblichen 
	 * 	(englischen) Format als Zeichenkette.
	 * @param {Boolean} withCurrencySymbol OPTIONAL Wenn dieser Parameter den 
	 * 	Wert true hat, dann wird ein Währungssymbol an die Währungsangabe 
	 * 	angehängt. Ist der Parameter nicht abgegeben, oder hat der Wert des 
	 * 	Parameters den Wert false, dann wird keine Abkuerzung oder Symbol an 
	 * 	die Währungsangabe angehängt. 
	 * @return {String} Konnte der Wert des Parameters currencyStr in eine 
	 * 	Dezimalzahl konvertiert werden, dann wird eine auf zwei 
	 * 	Nachkommastellen gerundete Währungsangabe zurueck gegeben, sonst 
	 * 	wird der Wert des Parameters currencyStr unveraendert zuruck gegeben.
	 */
	getLocalCurrency(currencyStr, withCurrencySymbol = false) {
		let currencyNum = Number.parseFloat(currencyStr);
		if (NaN !== currencyNum) {
			currencyNum = currencyNum.toFixed(2);
			const options = {
				style: 'currency',
				currency: 'EUR'
			};
			if (withCurrencySymbol)
				currencyStr = new Intl.NumberFormat('de-DE', options).format(currencyNum);
			else
				currencyStr = new Intl.NumberFormat('de-DE').format(currencyNum);
		}
		return currencyStr;
	}
	
	
	/**
	 * Finde zu einem Wert den ersten, entsprechenden Schluessel in einem 
	 * 	JavaScript Map-Objekt.
	 * 
	 * @param {Map} map Map-Objekt mit Schluessel/Wert-Paaren.
	 * @param {*} searchValue Suchwert
	 * @return {*|null} Wenn der Suchwert in der Map gefunden wird, dann 
	 * 	liefert die Methode den dazugehoerenden Schluessel. Wird der Suchwert 
	 * 	NICHT gefunden, dann liefert die Methode den null-Wert.
	 */
	findKey(map, searchValue) {
	  for (const [key, value] of map) {
	    if (value === searchValue) return key;
	  }
	  return null; // nicht gefunden
	}	
} // Utilities
