<?php


/**
 * php/models/model/rest/user.php
 *
 * REST (Representational State Transfer) User Model
 *  - REST-Schnittstelle mit Methoden zum Management eines Benutzers der SPA.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\HttpRequestModel as HttpRequest;
use HaHeLa\HttpResponseModel as HttpResponse;
use HaHeLa\UserModel as User;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


/**
 * REST (Representational State Transfer) User Model
 *  - REST-Schnittstelle mit Methoden zum Management eines Benutzers der SPA.
 */
class RestUserModel implements CommandInterface
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
     *   Fuer die Kompatibilitaet mit HaHeLa\SqldbModel von Bedeutung.
     * @var array
     */
    private $options = array();
    
    /**
     * Ausgabedaten (Rohdaten vor der JSON-Kodierung)
     * @var array Assoziatives Array
     */
    private $outputRawData;
    

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
     * @return HaHeLa\RestUserModel
     */
    public function execute ($request, $response)
    {
        $this->outputRawData = new \stdClass();
        if (HttpRequest::GET == $request->getMethod()) {
            // HTTP Methode GET - Read
            $this->outputRawData->requestMethod = 'GET (read)';
            $action = $request->getParameter('act', 'string');
            if ('info' === $action) {
                //
                // * Benutzerkontoinformationen **********************************************************************
                //
                try {
                    $user = new User($this->dbh, $this->config, $this->options);
                    //
                    // Benutzerkonto-Informationen vom OAuth2-Server
                    //
                    $userInfo = $user->getUserInfoFromOAuthServer();
                    // Beispiel einer Benutzerinformation vom OAuth2-Server:
                    // $userInfo = array(
                    //  sub ('subject') ist die eindeutige ID des Benutzers 
                    //  (End-Users), die vom Authorization Server (bzw. OpenID 
                    //  Provider) vergeben wird. sub ist ein standardisierter 
                    //  Claim im OAuth2/OpenID-Connect-Kontext.
                    //  "sub" => "1", 
                    //  Eindeutiger, einzigartiger (unique) Benutzername. 
                    //  "username" => "harald",
                    //  Kompetenz, Rolle des Benutzers.
                    //  "role" => "user"
                    // );
                    //
                    // Benutzerkonto-Informationen vom lokalen Server
                    //  mergen mit Benutzerkonto-Informationen vom 
                    //  OAuth2-Server.
                    //
                    $this->options = array('sub' => $userInfo['sub']);
                    $ids = $user->exists($this->options);
                    if (!empty($ids)) {
                        $userInfo = array_merge(
                            $userInfo,
                            $user->setId($ids[0])->read()->toArray()
                        );
                        $this->outputRawData->info = $userInfo;
                    }
                }
                catch (\Exception $e) {
                    $this->outputRawData = $this->getErrorObject($e);
                }
            }
        }
        elseif (HttpRequest::POST == $request->getMethod()) {
            // HTTP Methode POST - Create
            $this->outputRawData->requestMethod = 'POST (create)';
            $action = $request->getParameter('act', 'string');
            if ('save' === $action) {
                //
                // * Speichern der Benutzerkontoinformationen als neuen Datensatz ************************************
                // *    in der lokalen Datenbank.
                //
                try {
                    $user = new User($this->dbh, $this->config);
                    $user->setSub($request->getParameter('sub', 'integer'))
                    ->setFirstName($request->getParameter('first_name', 'string'))
                    ->setLastName($request->getParameter('last_name', 'string'));
                    $this->outputRawData->result = $user->create();
                }
                catch (\Exception $e) {
                    $this->outputRawData = $this->getErrorObject($e);
                }
            }
        }
        elseif (HttpRequest::PUT == $request->getMethod()) {
            // HTTP Methode PUT - Update
            $this->outputRawData->requestMethod = 'PUT (update)';
            $action = $request->getParameter('act', 'string');
            if ('save' === $action) {
                //
                // * Erneuern der Benutzerkontoinformationen  ********************************************************
                // *    in der lokalen Datenbank.
                //
                try {
                    $user = new User($this->dbh, $this->config);
                    $user->SetId($request->getParameter('id', 'integer'))
                    ->SetSub($request->getParameter('sub', 'integer'))
                    ->SetFirstName($request->getParameter('first_name', 'string'))
                    ->SetLastName($request->getParameter('last_name', 'string'));
                    $this->outputRawData->result = $user->update();
                }
                catch (\Exception $e) {
                    $this->outputRawData = $this->getErrorObject($e);
                }
            }
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
    
    
    /* ******************************************* private (Hilfs-)Methoden */
    
    /** 
     * Erstellt ein einfaches Fehlerobjekt.
     * 
     * @param \Exception $e
     * @return \stdClass
     */
    private function getErrorObject (\Exception $e): \stdClass
    {
        $return = new \stdClass();
        $return->errCode = $e->getCode();
        $return->errMessage = $e->getMessage();
        $return->errFile = $e->getFile();
        $return->errLine = $e->getLine();
        return $return;
    }
}
