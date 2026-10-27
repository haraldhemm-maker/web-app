<?php


/**
 * php/modules/model/http/request.php
 *
 * HTTP Request Model - Klasse fuer den Zugriff auf Parameter eines
 *   HTTP-Requests.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.1.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * HTTP Request Model - Klasse fuer den Zugriff auf Parameter eines
 *   HTTP-Requests.
 */
class HttpRequestModel implements HttpRequestInterface
{
    /**
     * HTTP Request Methoden (als Konstanten)
     */
    public const GET     = 'GET';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const HEAD    = 'HEAD';
    public const DELETE  = 'DELETE';
    public const TRACE   = 'TRACE';
    public const OPTIONS = 'OPTIONS';
    public const CONNECT = 'CONNECT';
    public const MERGE   = 'MERGE';
    public const PATCH   = 'PATCH';


    /**
     * Aktuelle HTTP Request Methode (immer in Grossbuchstaben)
     *
     * @var string $method
     * @default self::GET
     */
    protected string $method = self::GET;
    
    /**
     * Eingabedaten via HTTP Request Methode GET, POST, PUT, DELETE.
     * @var array Assoziatives Array
     */
    private array $inputRawData = array();


    /**
     * Constructor Methode. Erzeugt einen neuen HTTP Request.
     */
    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        if (self::GET == $this->method) {
            $this->inputRawData = $_GET;
        }
        elseif (self::POST == $this->method) {
            $this->inputRawData = $_POST;
        }
        elseif (self::PUT == $this->method) {
            parse_str(file_get_contents('php://input'), $this->inputRawData);
        }
        elseif (self::DELETE == $this->method) {
            parse_str(file_get_contents('php://input'), $this->inputRawData);
        }
        else {
            // Hier ist Platz fuer die weiteren HTTP Request Methoden
            // HEAD, TRACE, OPTIONS, CONNECT, MERGE und PATCH.
        }
    }


    /**
     * Holt die aktuelle HTTP Request Methode.
     *
     * @return string Aktuelle HTTP Request Methode (immer in Grossbuchstaben).
     */
    public function getMethod(): string
    {
        return $this->method;
    }


    /**
     * Ueberprueft, ob ein benannter Parameter uebermittelt wurde.
     *
     * @param string $name Name eines HTTP_Request Parameters.
     * @return boolean Wenn der benannte Parameter uebermittelt wurde, 
     *   dann liefert die Methode den Wert TRUE, sonst FALSE.
     */
    public function issetParameter(string $name): bool
    {
        return isset($this->inputRawData[$name]);
    }


    /**
     * Gibt den Wert eines benannten und uebermittelten Parameters zurueck.
     *
     * @param string $name Name eines HTTP_Request Parameters.
     * @param string $type OPTION Typ des HTTP_Request Parameters. 
     *  Die optionale Angabe eines Types fuehrt zur Umwandlung des Parameters 
     *  in den angegebenen Typ. 
     *  Moegliche Werte sind zur Zeit:
     *      'array', 'bool', boolean', 'float', 'int', 'integer', 'object', 
     *      'string' und ''.
     *  Wird ein Leerstring oder ein anderer Typ als oben genannt angegeben,
     *  so erfolgt KEINE Umwandlung.
     *  Wird als Type 'string' angegeben, so erfolgt zusaetzlich noch eine
     *  Umwandlung in den UTF8-Zeichensatz.
     * @return mixed Wert eines benannten und uebermittelten Parameters. 
     *  Wurde der benannte Parameter nicht uebermittelt, dann gibt die 
     *  Methode den Wert FALSE zurueck.
     */
    public function getParameter(string $name, string $type = ''): mixed
    {
        if (isset($this->inputRawData[$name])) {
            $parameter = $this->inputRawData[$name];
            switch ($type) {
                case 'array':   $parameter = (array)$parameter; 
                break;
                case 'bool':
                case 'boolean': $parameter = (boolean)$parameter; 
                break;
                case 'float':   $parameter = (float)$parameter; 
                break;
                case 'int':
                case 'integer': $parameter = (integer)$parameter; 
                break;
                case 'object':  $parameter = (object)$parameter; 
                break;
                case 'string':  $parameter = utf8_encode((string)$parameter); 
                break;
            }
            return $parameter;
        }
        return FALSE;
    }


    /**
     * Liefert eine Liste der Namen aller im HTTP-Request uebermittelten
     *   Parameter zurueck.
     *
     * @return array Liste der Namen aller im Request uebermittelten 
     *   Parameter.
     */
    public function getParameterNames(): array
    {
        return array_keys($this->inputRawData);
    }


    /**
     * Liefert den Wert eines benannten Headers.
     *
     * @param string $name Header-Name.
     * @return mixed Wert des benannten Headers oder FALSE, falls der 
     *   benannte Header nicht vorhanden ist.
     */
    public function getHeader(string $name): mixed
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        return FALSE;
    }


    /**
     * Holt die Rohdaten des HTTP-Requests. Die Rohdaten liegen in einem
     *   assoziatives Array vor.
     *
     * @return array HTTP-Request-Rohdaten.
     */
    public function getInputRawData(): array
    {
        return $this->inputRawData;
    }


  /* ********************************************** private (Hilfs-)Methoden */


}