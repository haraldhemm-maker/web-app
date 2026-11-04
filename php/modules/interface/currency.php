<?php


/**
 * php/modules/interface/currency.php
 *
 * Currency Interface - Interface des Modells einer Waehrung (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Currency Interface - Interface des Modells einer Waehrung (singular).
 */
interface CurrencyInterface
{
    /**
     * Konstruktor
     *
     * @param resource $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer eines
     *   Waehrungsdatensatzes in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten der entsprechenden Waehrung 
     *   aus der Datenbank gelesen und die Objekteigenschaften 
     *   entsprechend initialisiert. 
     */
    public function __construct($dbh, $config, $options = array());


    /**
     * Erzeugt einen Waehrungsdatensatzes in der Datenbank.
     * 
     * @return integer Identifikationsnummer des Waehrungsdatensatzes
     *   in der Datenbank.
     */
    public function create();


    /**
     * Aktualisiert den Waehrungsdatensatz. 
     * 
     * @return boolean Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update();


    /**
     * Liest den Waehrungsdatensatz, der durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert wird. 
     *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
     *  der Datenbank vorhanden, dann werden die Eigenschaften des
     *  Waehrungsobjekts mit vorgegebenen Werten gefuellt.
     *
     * @return Modules_Model_Currency Gibt das Objekt selbst zurueck 
     *   (fluent interface).
     */
    public function read();


    /**
     * Loescht den Waehrungsdatensatz aus der Datenbank. 
     *   Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *   in der Eigenschaft $id repraesentiert.
     *
     * @return boolean Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete();


    /**
     * Ueberprueft ob eine Waehrung in der Datenbank existiert.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *   Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return int|false Wenn die Waehrung gefunden wird, dann wird dessen 
     *   Identifikationsnummer zurueckgegeben, sonst false.
     */
    public function exists();


    /**
     * Sucht die Waehrung, deren Wert im Datenbankfeld 'name_en' dem 
     *  Namensfragment aehneln. 
     *   
     * @param string $nameFragment Fragment des Waehrungsnamen.
     * @return array Feld mit den Identifikationsnummern der 
     *   Waehrungen, deren Wert im Datenbankfeld 'name_en' dem 
     *   Namensfragment aehneln.
     */
    public function searchByName($nameFragment);


    /**
     * Uebertraegt die wichtigsten Eigenschaften eines Waehrungsdatensatzes 
     *  in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass;


    /**
     * Setzt ausgewaehlte, proteced Eigenschaften eines Objektes der Instanz 
     *  der Waehrungsklasse auf vordefinierte Werte zurueck.
     *
     * @return HaHeLa\CurrencyModel
     */
    public function resetProperties();
}