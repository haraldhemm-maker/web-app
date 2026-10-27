<?php


/**
 * php/models/model/rest/system.php
 *
 * REST (Representational State Transfer) System Model
 *  - REST-Schnittstelle mit Methoden zur Initialisierng der SPA.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @internal curl Command:
 *   curl -G http://localhost/~harald/app/web-app/php/controllers/rest/v1/controller.php -d "cmd=System&act=get_routes"
 *
 * @internal Uses the Document Object Model (DOM) extension of PHP. The DOM
 *   extension allows you to operate on XML documents through the DOM API
 *   with PHP. This extension is enabled by default.
 */


namespace HaHeLa;


use HaHeLa\HttpRequestModel as HttpRequest;
use HaHeLa\HttpResponseModel as HttpResponse;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


/**
 * REST (Representational State Transfer) System Model
 *   - REST-Schnittstelle mit Methoden zur Initialisierng der SPA.
 */
class RestSystemModel implements CommandInterface
{
    /**
     * Datenbank-Handle
     * @var resource
     */
    private $dbh;
    
    /**
     * Konfigurationsdaten des Systems.
     * @var array
     */
    private $config;
    
    /**
     * Optionen (zur Zeit ungenutzt)
     *   Fuer die Kompatibilit�t mit HaHeLa\SqldbModel von Bedeutung.
     * @var array
     */
    private $options = array();
    
    /**
     * Ausgabedaten (Rohdaten vor der JSON-Kodierung)
     * @var array Assoziatives Array
     */
    private $outputRawData;
    
    /**
     * Dateinamenerweiterungen aller JavaScript-Dateien.
     * @var array
     */
    private $jsFileExtensions = array('cnf', 'js', 'map');
    
    /**
     * Dateinamenerweiterung aller HTML-Dateien. Alle Zeichen nach dem Punkt des Dateihauptnames werden als
     *   eine Dateinamenerweiterung behandelt.
     * @var array
     */
    private $htmlFileExtensions = array('htm', 'html');


    /**
     * Konstruktor
     *
     * @param resource $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     */
    public function __construct ($dbh, $config)
    {
        // Datenbank-Handle und Konfiguration sichern.
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = array(); // zur Zeit unbenutzt.
    }
    
    
    /**
     * Ausfuehren des REST Kommandos.
     *
     * @param HaHeLa\HttpRequestModel $request
     * @param HaHeLa\HttpResponseModel $response
     * @return HaHeLa\RestSystemModel
     */
    public function execute ($request, $response)
    {
        $this->outputRawData = new \stdClass();
        if (HttpRequest::GET == $request->getMethod()) {
            // HTTP Methode GET - Read
            $this->outputRawData->requestMethod = 'GET (read)';
            $action = $request->getParameter('act', 'string');
            if ('get_menu_map' == $action || 'get_routes' == $action) {
                //
                // ******************************************************************* Hole die Menuedaten bzw Routen.
                //
                $menuMap = array();
                $path = __DIR__.DS.'..'.DS.'..'.DS.'..'. DS.'..'.DS.'htm'.DS.'pages'.DS;
                $doc = new \DOMDocument();
                $this->buildMenuMap($path, $menuMap, $doc);
                $this->outputRawData->map = $menuMap;
            }
        }
        elseif (HttpRequest::POST == $request->getMethod()) {
            // HTTP Methode POST - Create
            $this->outputRawData->requestMethod = 'POST (create)';
        }
        elseif (HttpRequest::PUT == $request->getMethod()) {
            // HTTP Methode PUT - Update
            $this->outputRawData->requestMethod = 'PUT (update)';
        }
        elseif ('DELETE' == $_SERVER['REQUEST_METHOD']) {
            // HTTP Methode DELETE - Delete
            $this->outputRawData->requestMethod = 'DELETE (delete)';
        }
        else {
            // HTTP Methode: HEAD, TRACE, OPTIONS, CONNECT, MERGE, PATCH
            $this->outputRawData->requestMethod = 'HEAD | TRACE | OPTIONS | CONNECT | MERGE | PATCH';
        }
        $response->addHeader('Content-Type','application/json; charset=utf-8');
				$response->write(json_encode($this->outputRawData)."\n");
        return $this;
    }
    
    
  /* ********************************************** private (Hilfs-)Methoden */
    
    /**
     * Aufbau einer Menue-Map.
     *  Wenn in der Sesiion ein Access Token vorhanden ist, dann wird die 
     *  Login-Seite aus der Menue-Map entfernt.
     * 
     * @param string $path Pfad zu den HTML-Seiten-Fragmenten (Pfad mit 
     *  terminierendem Backslash bzw. Slash).
     * @param array $map Assoziatives Array mit den Informationen zu 
     *  HTML-Seiten-Fragmenten: 
     *      menu_display    : Wenn der Wert dieses Feldes 'None' ist, dann soll 
     *                          das Seiten-Fragment im Menue NICHT angezeigt 
     *                          werden.
     *      menu_name       : In diesem Feld steht der Name, wie er im Menue 
     *                          angezeigt werden soll.
     *      menu_sort       : Diese Zahl ist die Position im Menue. Die Zahl 0 
     *                          steht fuer die 1.te Position von links bzw, 
     *                          ganz oben.
     * @param \DOMDocument() $doc Instanz der Klasse DOMDocument.
     * @return void
     */
    private function buildMenuMap (&$path, &$map, &$doc)
    {
        // Session wurde bereits vom Kontroller gestartet.
        if (empty($_SESSION['access_token'])) {
            // Menue-Map, falls bisher noch keine Anmeldung erfolgte.
            foreach ($this->config['CLIENT']['PAGES'] as $key => $json) {
                $value = json_decode($json);
                $map[$key] = $value;
            }
            uasort($map, 'self::cmpDataMenuSort');        
        }
        else {
            // Menu-Map fuer angemeldete Benutzer.
            foreach(scandir($path) as $entry) {
                if ('.' != $entry && '..' != $entry) {
                    $name = $path . $entry;
                    if (is_dir($name)) {
                        $name .= DS;
                        $this->buildMenuMap($name, $map, $doc);
                    }
                    else {
                        // Lade eine HTML-"Seite" (Lade das Fragment einer HTML-Seite).
                        libxml_use_internal_errors(true); // @internal HaHeLa [2021-01-19] Necessary to handle HTML 5 tags.
                        $result = $doc->loadHTMLFile($name);
                        libxml_clear_errors();
                        if (false !== $result) {
                            $nodeList = $doc->getElementsByTagName('div'); // Hole alle div-Tags.
                            $node = $nodeList->item(0); // Das erste div-Tag enthaelt die Menueinformationen.
    						if (!is_null($node) && XML_ELEMENT_NODE === $node->nodeType) {
                            	$key = $node->getAttribute('id');
                            	$value = new \stdClass();
                            	$value->menu_display = $node->getAttribute('data-menu_display');
                            	$value->menu_name = $node->getAttribute('data-menu_name');
                            	$value->menu_sort = $node->getAttribute('data-menu_sort');
                            	$map[$key] = $value;
    							unset($value);
    						}
                        }
                    }
                }
            }
            // Login-Seite aus der Menue-Map entfernen.
            unset($map['login']);
        }
        uasort($map, 'self::cmpDataMenuSort');        
    }
    
    
    /**
     * Die Vergleichsfunktion zum Sortieren des assoziative Array mit den Menuedaten.
     *
     * @param \stdClass $a Menuedaten
     * @param \stdClass $b Menuedaten
     * @return int Sind die Werte der Eigenschaft "menu_sort" der beiden Objecte $a und $b gleich, dann liefert die
     *   Methode den Wert 0. Ist der Wert der Eigenschaft "menu_sort" von Object $a groesser als der von Object $b,
     *   dann wird der Wert 1 zurueckgegeben. Ist der Wert kleiner dann -1.
     */
    private function cmpDataMenuSort ($a, $b)
    {
        if ($a->menu_sort == $b->menu_sort) {
            return 0;
        }
        return ($a->menu_sort > $b->menu_sort)? +1: -1;
    }    
}