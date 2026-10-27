<?php


/**
 * php/modules/interface/http/response.php
 *
 * HTTP Request Interface - Interface der Klasse HttpResponseModel.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Interface der Klasse HttpResponseModel.
 */
interface HttpResponseInterface
{
    /**
     * Constructor Methode. Erzeugt eine neue HTTP Response.
     */
    public function __construct ();
    
    
    /**
     * Holt bereits vorhandene HTTP Body Daten.
     *
     * @return mixed Inhalt der Eigenschaft $body.
     */
    public function getBody ();

    
    /**
     * Setzt den HTTP-Status der Antwort. Damit kann man z.B. signalisieren,
     *   dass eine ungueltige Anfrage gesendet wurde.
     *
     * @param string $status HTTP-Status der Antwort.
     * @return HttpResponseModel
     */
    public function setStatus ($status);
    
    
    /**
     * Fuegt einen Header hinzu, der spaeter an den Browser gesendet wird.
     *
     * @param string $name Name des Headers.
     * @param mixed $value Wert des Headers.
     * @return HttpResponseModel
     */
    public function addHeader ($name, $value);
    
    
    /**
     * Fuegt den Ausgabedaten weitere Daten hinzu.
     *
     * @param mixed $data Daten
     * @return HttpResponseModel
     */
    public function write ($data);
    
    
    /**
     * Sendet alle Daten an den Client (Browser).
     *
     * @return HttpResponseModel
     */
    public function flush ();
}