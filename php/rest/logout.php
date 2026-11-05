<?php


/**
 * php/models/model/rest/logout.php
 *
 * REST (Representational State Transfer) Logout Model
 *  - REST-Schnittstelle mit Methoden zum Logout von der SPA.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @internal curl Command:
 *   curl -G http://localhost/~harald/app/web-app/php/controllers/rest/v1/controller.php -d "cmd=Logout&act=logout"
 */


namespace HaHeLa;


use HaHeLa\HttpRequestModel as HttpRequest;
use HaHeLa\HttpResponseModel as HttpResponse;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


/**
 * REST (Representational State Transfer) Logout Model
 *  - REST-Schnittstelle mit Methoden zum Logout von der SPA.
 */
class RestLogoutModel implements CommandInterface
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
            if ('logout' === $action) {
                //
                // ***************************************************************************************** Abmelden.
                //
                $this->logout();
                $this->outputRawData->result = TRUE;
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
    
    
    /* ******************************************* private (Hilfs-)Methoden */
    
    private function logout(): void
    {
        // ---------------------------------------------------------
        // 1. Tokens beim AS widerrufen (revoked = 1 in der DB)
        // ---------------------------------------------------------
        $accessToken  = $_SESSION['oauth']['access_token']  ?? '';
        $refreshToken = $_SESSION['oauth']['refresh_token'] ?? '';
        $revokeUrl    = $this->config['AUTH']['URL_TOKEN_REVOKE'];
        $clientId     = $this->config['CLIENT']['ID'];
        foreach ([$accessToken, $refreshToken] as $token) {
            if ($token === '') {
                continue;
            }
            $ch = curl_init($revokeUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'token'     => $token,
                    'client_id' => $clientId,
                    // 'client_secret' => '...', // nur bei konfidentiellen Clients
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT  => 2,
                CURLOPT_TIMEOUT         => 3,
            ]);
            $result = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);
            // RFC 7009: Erfolg = 200, unbekanntes Token = ebenfalls 200
            // 401 = Client-Auth fehlgeschlagen → das wäre ein echter Fehler
            if ($httpCode !== 200) {
                // Loggen, aber Logout trotzdem fortsetzen
                error_log("Token-Revocation fehlgeschlagen (HTTP $httpCode): $result");
            }
        }        
        // -------------------------------------------------------------------
        // 2. Lokale Session komplett zerstören
        // -------------------------------------------------------------------
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}