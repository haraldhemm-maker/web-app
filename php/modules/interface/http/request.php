<?php


/**
 * php/modules/interface/http/request.php
 *
 * HTTP Request Interface - Interface der Klasse HttpRequestModel.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0@
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * HTTP Request Interface - Interface der Klasse HttpRequestModel.
 */
interface HttpRequestInterface
{
    /**
     * Constructor Methode. Erzeugt einen neuen HTTP Request.
     */
    public function __construct();


    /**
     * Holt die aktuelle HTTP Request Methode.
     *
     * @return string Aktuelle HTTP Request Methode (immer in Grossbuchstaben).
     */
    public function getMethod(): string;


    /**
     * Ueberprueft, ob ein benannter Parameter uebermittelt wurde.
     *
     * @param string $name Name eines HTTP_Request Parameters.
     * @return boolean Wenn der benannte Parameter uebermittelt wurde, 
     *   dann liefert die Methode den Wert TRUE, sonst FALSE.
     */
    public function issetParameter(string $name): bool;


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
    public function getParameter(string $name, string $type = ''): mixed;


    /**
     * Liefert eine Liste der Namen aller im HTTP-Request uebermittelten
     *   Parameter zurueck.
     *
     * @return array Liste der Namen aller im Request uebermittelten 
     *   Parameter.
     */
    public function getParameterNames(): array;


    /**
     * Liefert den Wert eines benannten Headers.
     *
     * @param string $name Header-Name.
     * @return mixed Wert des benannten Headers oder FALSE, falls der 
     *   benannte Header nicht vorhanden ist.
     */
    public function getHeader(string $name): mixed;


    /**
     * Holt die Rohdaten des HTTP-Requests. Die Rohdaten liegen in einem
     *   assoziatives Array vor.
     *
     * @return array HTTP-Request-Rohdaten.
     */
    public function getInputRawData(): array;
}
