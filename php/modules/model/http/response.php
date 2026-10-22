<?php
/**
 * php/modules/model/http/response.php
 *
 * HTTP Response Model - Klasse fuer eine HTTP-Response.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2020-2025 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */
namespace HaHeLa;


/**
 * HTTP Response Model - Klasse fuer eine HTTP-Response.
 */
class Modules_Model_Http_Response implements Modules_Interface_Http_Response
{
    /**
     * HTTP Status
     *
     * @var string
     * @default '200 OK';
     */
    private $status = '200 OK';

    /**
     * HTTP Header
     *
     * @var array
     */
    private $headers = array();

    /**
     * HTTP Body
     * @var string
     */
    private $body;


    /**
     * Constructor Methode. Erzeugt eine neue HTTP Response.
     */
    public function __construct ()
    {
    }
    
    
    /**
     * Holt bereits vorhandene HTTP Body Daten.
     * 
     * @return mixed Inhalt der Eigenschaft $body.
     */
    public function getBody () 
    {
        return $this->body;
    }


    /**
     * Setzt den HTTP-Status der Antwort. Damit kann man z.B. signalisieren,
     *   dass eine ungueltige Anfrage gesendet wurde.
     *
     * @param string $status HTTP-Status der Antwort.
     * @return HttpResponseModel
     */
    public function setStatus ($status)
    {
        $this->status = $status;
        return $this;
    }


    /**
     * Fuegt einen Header hinzu, der spaeter an den Browser gesendet wird.
     *
     * @param string $name Name des Headers.
     * @param mixed $value Wert des Headers.
     * @return HttpResponseModel
     */
    public function addHeader ($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }


    /**
     * Fuegt den Ausgabedaten weitere Daten hinzu.
     *
     * @param mixed $data Daten
     * @return HttpResponseModel
     */
    public function write ($data)
    {
        $this->body .= $data;
        return $this;
    }


    /**
     * Sendet alle Daten an den Client (Browser).
     *
     * @return HttpResponseModel
     */
    public function flush ()
    {
        header("HTTP/1.1 {$this->status}");
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        print $this->body;
        $this->headers = array();
        $this->body = null;
     }


  /* ********************************************** private (Hilfs-)Methoden */


}