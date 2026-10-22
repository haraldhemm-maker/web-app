  
  // Ereighnisverwaltung des Suchsfeldes.
  document.getElementById('search_simple_query').addEventListener('keydown', (evt) => {
      if (evt.key === 'Enter') {
          evt.preventDefault(); // Verhindert das Neuladen der Seite durch Formular-Submit
          //let url = 'http://localhost/~harald/app/tv-widget/frontend.html#search';
          let url = this.config.base.path + this.config.base.frontend_page + '#search';
          document.location.assign(url);
      }
  });

}


/**
 * Ueberprueft, ob der Benutzer am System angemeldet ist.
 * @returns {Boolean} Wenn der Benutzer am System angemeldet ist, 
 *   dann liefert die Methode den Wert true, sonst false.
 */
isUserLoggedIn () {
  return (0 < window._SESSION_ID.length && 0 < window._ACCESS_TOKEN.length);
}


/**
 * Fuegt den vorhandenen Routen eine weitere hinzu.
 * @param {String} pageId Identifikationszeichenkette einer Seite.
 * @param {String} pageMenuName
 * @param {Number} pageMenuSort
 * @returns {Router} Gibt das Objekt selbst zurueck ("sprechende Schnittstelle“).
 */  
addRoute (pageId, pageMenuName, pageMenuSort = 0) {
  this.routes.set(pageId, {menu_name: pageMenuName, menu_sort: pageMenuSort});
  return this;
}


/**
 * Entfernt eine Route aus der Liste der vorhandenen Routen.
 * @param {String} pageId Identifikationszeichenkette einer Seite.
 * @returns {Router} Gibt das Objekt selbst zurueck ("sprechende Schnittstelle“).
 */
removeRoute (pageId) {
  this.routes.delete(pageId);
  return this;
}


/**
 * Ermittelt die moeglichen Routen (Navigationsmoeglichkeiten) zu 
 *   vorhandenen Seiten (Container) und gibt diese als eine Sammlung 
 *   von Schluessel/Wert-Paaren (Map) zurueck. Die Schluessel sind die 
 *   Identifikationszeichenketten der einzelnen Seiten (Container) und 
 *   die entsprechenden Werte sind Objekte unter anderem mit den Namen 
 *   der Seiten (Container) wie diese im Menue erscheinen sollen.
 * @returns {Map} Sammlung von Schluessel/Wertpaaren moeglicher Routen.  
 * @note Fuer die Nutzung dieser Methode NUSS der Benutzer angemeldet sein.
 */
getRoutes () {
  this.client.httpParams.clear()
  .set('cmd', 'System')
  .set('act', 'get_routes');
  return this.client.exec('GET')
  .then(response => { // Kein "interner" Fehler
    if ('undefined' == typeof response.errCode || 0 == response.errCode) {
      let responseMap = response.map;
      responseMap = Object.entries(responseMap);
      return new Map(responseMap);
    }
    else {
      console.log(response);
      return response;
    }
  }).catch(error => { // "Interner" Fehler beim XMLHttpRequest.
    console.error(error.message, error.fileName, error.lineNumber);
    return error;
  });
}
  
  
  /**
   * Holt den Uniform Resource Identifier (URI) einer HTML-Seite.
   * 
   * @param {String} pageId Identifikationszeichenkette einer Seite.
   * @return {String} URI Uniform Resource Identifier einer Seite.
   */
  getPageUri (pageId) {
      let uri = this.config.base.path + 'htm/pages/';
      const parts = pageId.split('-');
      let firstPass = true;
      for (let i = 0; i < parts.length; i++) {
          if (firstPass) {
              uri += parts[i];
              firstPass = false;
          }
          else uri += '/' + parts[i];
      }
      uri += '.inc.htm';
      return uri;
  }


