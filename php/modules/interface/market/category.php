<?php


/**
 * php/modules/interface/market/category.php
 *
 * Market Category Interface - Interface des Modells einer Handelsplatzkategorie (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Market Category Interface - Interface des Modells einer Handelsplatzkategorie (singular).
 */
interface MarketCategoryInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options (optional) Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer einer
     *   Handelplatzkategorie in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten des entsprechenden 
     *   Handelplatzes aus der Datenbank gelesen und die 
     *   Objekteigenschaften entsprechend initialisiert. 
     */
    public function __construct (\PDO $dbh, array $config, array $options = array());


    /**
     * Holt die Optionen
     *
     * @return array Optionen
     */
    public function getOptions (): array;


    /**
     * Setzt die Optionen
     *
     * @param array $options Optionen
     * @return \HaHeLa\MarketCategoryModel
     */
    public function setOptions (array $options): \HaHeLa\MarketCategoryModel;


    /**
     * Holt die Identifikationsnummer der Handelplatzkategorie in der 
     *  Datenbank.
     *
     * @return int Identifikationsnummer der Handelplatzkategorie.
     */
    public function getId (): int;


    /**
     * Setzt die Identifikationsnummer der Handelplatzkategrie.
     *
     * @param int $id Identifikationsnummer der Handelplatzkategorie.
     * @return \HaHeLa\MarketCategoryModel
     */
    public function setId (int $id): \HaHeLa\MarketCategoryModel;


    /**
     * Holt den 4-Grossbuchstabencode der Handelplatzkategorie in der 
     *  Datenbank.
     *
     * @return string 4-Grossbuchstabencode der Handelplatzkategorie.
     */
    public function getCode (): string;


    /**
     * Setzt den 4-Grossbuchstabencode der Handelplatzkategrie.
     *
     * @param string $code 4-Grossbuchstabencode der Handelplatzkategorie.
     * @return \HaHeLa\MarketCategoryModel
     */
    public function setCode (string $code): \HaHeLa\MarketCategoryModel;


    /**
     * Holt den Namen (die Bezeichnung) der Handelsplatzkategorie in der 
     *  entsprechenden Sprache.
     * 
     * @param string $language 2-Kleinbuchstabenkennung der Sprache.
     * @return string   Namen (Bezeichnung) der Handelsplatzkategorie 
     *  in der entsprechenden Sprache.
     */ 
    public function getName (string $language = 'en'): string;


    /**
     * Setzt den Namen (die Bezeichnung) der Handelsplatzkategorie in der 
     *  entsprechenden Sprache.
     * 
     * @param string $name Name (Bezeichnung) der Handelsplatzkategorie.
     * @param string $language 2-Kleinbuchstabenkennung der Sprache.
     * @return \HaHeLa\MarketCategoryModel
     */
    public function setName (string $name, string $language = 'en'): \HaHeLa\MarketCategoryModel;


    /**
     * Erzeugt einen Marktplatzkategoriedatensatz in der Datenbank.
     * 
     * @return int Identifikationsnummer des 
     *  Markplatzkategoriedatensatzes in der Datenbank.
     */
    public function create (): int;


    /**
     * Aktualisiert den Markplatzkategoriedatensatz. 
     * 
     * @return bool Ergebnis der Aktualisierung. TRUE - die Aktualisierung
     *  war erfolgreich. FALSE - der Datensatz wurde nicht aktualisert.
     */
    public function update (): bool;


    /**
     * Liest den Marktplatzkategoriedatensatz, der durch die 
     *  Identifikationsnummer in der Eigenschaft $id repraesentiert wird. 
     *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
     *  der Datenbank vorhanden, dann werden die Eigenschaften des
     *  Marktplatzkategorieobjekts mit vorgegebenen Werten gefuellt.
     *
     * @return \HaHeLa\MarketCategoryModel Gibt das Objekt selbst zurueck 
     *   (fluent interface).
     */
    public function read (): \HaHeLa\MarketCategoryModel;


    /**
     * Loescht den Markplatzkategoriedatensatz aus der Datenbank und setzt 
     *  die ausgewaehlten Objekteigenschaften auf Standardwerte zurueck.
     *  Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert.
     *
     * @return bool Die Methode liefert den boolschen Wert TRUE, 
     *   falls die Daten geloescht werden konnten, sonst FALSE.
     */
    public function delete (): bool;


    /**
     * Setzt ausgewaehlte, proteced Eigenschaften eines Objektes der Instanz 
     *  der Marktplatzklasse auf vordefinierte Werte zurueck.
     *
     * @return \HaHeLa\MarketCategoryModel
     */
    public function resetProperties (): \HaHeLa\MarketCategoryModel;


    /**
     * Ueberprueft ob Markplaetze mit den gegebenen Eigenschaften in der 
     *  Datenbank existieren.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *   Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return FALSE|array Wenn Markplaetze gefunden werden, dann werden 
     *  deren Identifikationsnummern zurueckgegeben, sonst FALSE.
     */
    public function exists (): FALSE|array;


    /**
     * Uebertraegt die wichtigsten Eigenschaften eines 
     *  Marktplatzkategoriedatensatzes in ein Objekt einer 
     *  Standardklasse und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject (): \stdClass;


    /**
     * Pruefe die Syntax des Marktkategoriecodes
     *  (Market Category Code)
     * 
     * @param string $mcc Marktkategoriecode
     * @return bool Wenn der Marktkategoriecode eine gueltige Syntax hat, 
     *  dann liefert die Methode den boolschen Wert TRUE, sonst FALSE.
     */
    public function validateMarketCategoryCodeSyntax (string $mcc): bool;
}