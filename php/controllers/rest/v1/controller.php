<?php


/**
 * php/controllers/rest/v1/controller.php
 *
 * REST (Representational State Transfer) Controller Version 1.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\ConfigurationModel as Configuration;
use HaHeLa\HttpRequestModel as HttpRequest;
use HaHeLa\HttpResponseModel as HttpResponse;
use HaHeLa\CommandResolverFilesystemModel as CommandResolverFilesystem;


if (!defined('TEST_HAHELA')) {
    define('TEST_HAHELA', false);
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// autoloader
include_once '..'.DS.'..'.DS.'..'.DS.'inc'.DS.'autoloader.inc.php';


// Konfiguration und Datenbank-Handle.
$phpConfiguration = new Configuration();
$configuration = $phpConfiguration->read($notConfidential = FALSE);
include_once '..'.DS.'..'.DS.'..'.DS.'inc'.DS.'database.inc.php';


// Session Management und Session starten.
//@todo Soll in einer spaeteren Entwicklung realisiert werden.
//include_once '..'.DS.'..'.DS.'..'.DS.'includes'.DS.'session.inc.php';

// Web-App (Apache)
ini_set('session.save_path', '/home/harald/public_html/app/web-app/tmp/sessions');
session_start();


// Zugriff auf Parameter des HTTP-Requests ermoeglichen.
$request = new HttpRequest();


// Wenn kein access token vorhanden ist, dann kein Zugriff auf die API 
//  zulassen. Eine Fehlermeldung zurueckgeben.
if (empty($_SESSION['access_token'])) {
	// Kein "access token" vorhanden. 
    // Zugriff auf das API verweigern. Jedoch gibt es folgende Ausnahme:
    //  * Request-Methode ist 'GET', Request-Parameter 'cmd' ist gleich 
    //      'System' und Request-Parameter 'act' ist gleich 'get_menu_map'.
    $apiMethod = $request->getMethod();
    $apiCommand = $request->getParameter('cmd', 'string');
    $apiAction = $request->getParameter('act', 'string');
    // Wenn die Ausnahmebedingungen nicht zutreffen, dann erfolgt die Ausgabe 
    //  einer Fehlermeldung.    
    if (HttpRequest::GET !== $apiMethod 
        || 'System' !== $apiCommand
        || 'get_menu_map' !== $apiAction
    ) {
        // Ausgabe einer Fehlermeldung im json-Format.
        $output = new \stdClass();
        $output->errCode = 1;
        $output-> errMessage = 'Keine Zugriffberechtigung auf das REST API.';
        $output->errFile = 'php/controllers/rest/v1/controller.php';
        $output->errLine = 56;
        $response = new HttpResponse();
        $response->write(json_encode($output));
        $response->flush();
        exit;        
    }
}


// Kommando ausfuehren.
$response = new HttpResponse();
// Relativer Pfad zu den REST-Modellen.
$path = '..'.DS.'..'.DS.'..'.DS.'rest'; // ohne abschliessenden Dateiseparator.
// Voreingestelltes Kommando.
$defaultCommand = 'Login'; // Beachte Gross/Kleinschreibung
// Kommando ausfuehren.
$resolver = new CommandResolverFilesystem($dbh, $configuration, $path, $defaultCommand);
$command = $resolver->getCommand($request);
$command->execute($request, $response);
$response->flush();
