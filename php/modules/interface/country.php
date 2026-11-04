<?php


/**
 * php/modules/interface/country.php
 *
 * Country Interface - Interface des Modells eines Landes (Staates) (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Country Interface - Interface des Modells eines Landes (Staates) (singular).
 */
interface CountryInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer eines
     *   Landes- (Staats)-Datensatzes in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten des entsprechenden Landes 
     *   (Staats) aus der Datenbank gelesen und die Objekteigenschaften 
     *   entsprechend initialisiert. 
     */
    public function __construct(\PDO $dbh, array $config, array $options = array());


    /**
     * Holt den Namen des Landes (Staats) in deutscher oder englischer Sprache.
     * 
     * @param string $language Optionaler Parameter der Sprache. 
     *  Voreingestellt ist die deutsche Sprache. 
     * @return string Name des Landes (Staats) in deutscher oder englischer 
     *  Sprache.
     */
    public function getName(string $language = 'de'): string;


    /**
     * Setzt den Namen eines Landes (Staats). Die voreingestellte Sprache 
     *  ist Deutsch. Moeglich ist auch die englische Sprache.
     * 
     * @param string Name des Landes (Staats) in deutscher oder englischer 
     *  Sprache.
     * @param string $language Optionaler Parameter der Sprache. 
     *  Voreingestellt ist die deutsche Sprache. 
     * @return CountryModel
     */
    public function setName(string $name, string $language = 'de'): CountryModel;


    /**
     * Erzeugt einen Landes- (Staats-)Datensatzes in der Datenbank. 
     *  Der Landes- (Staats-)name in deutscher Sprache ist zwingend 
     *  erforderlich. Der englische Landes- (Staats-)name ist optional. 
     * 
     * @return integer Identifikationsnummer des Landes- (Staats-)Datensatzes
     *  in der Datenbank. 
     */
    public function create(): int;


    /**
     * Aktualisiert den Landes- (Staats-)Datensatz. 
     *  Der Landes- (Staats-)name in deutscher Sprache ist zwingend 
     *  erforderlich. Der englische Landes- (Staats-)name ist optional. 
     * 
     * @return boolean Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update(): bool;


    /**
     * Liest den Landes- (Staats-)Datensatz, der durch die 
     *  Identifikationsnummer in der Eigenschaft $id repraesentiert wird. 
     *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
     *  der Datenbank vorhanden, dann werden die Eigenschaften des 
     *  Landesobjekts mit vorgegebenen Werten gefuellt.
     *
     * @return CountryModel Gibt das Objekt selbst zurueck 
     *   (fluent interface).
     */
    public function read(): CountryModel;


    /**
     * Loescht den Landes- (Staats-)Datensatz aus der Datenbank. 
     *  Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert.
     *
     * @return boolean Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete(): bool;


    /**
     * Ueberprueft, ob ein Land (Staat) in der Datenbank existiert.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *  als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *  Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *  Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return int|false Wenn das Land (Staat) gefunden wird, dann wird 
     *  dessen Identifikationsnummer zurueckgegeben, sonst false.
     */
    public function exists(): int|false;


    /**
     * Sucht die Landes- (Staats-)Datensaetze deren Wert im Datenbankfeld
     *   'name_de' ODER 'name_en' dem Namensfragment aehneln. 
     * 
     * @param string $nameFragment Fragment des Landes- (Staats-)namen in 
     *  deutscher oder englischer Sprache.
     * @param string $language Optionale Angabe der Sprache, Voreingestellt 
     *  ist die deutsche Sprache. Englisch ist eine weitere 
     *  Sprachalternative.
     * @return array Feld mit den Identifikationsnummern der 
     *  Landes- (Staats-)Datensaetze deren Wert im Datenbankfeld 'name_de'
     *  ODER 'name_en' dem Namensfragment aehneln.
     */
    public function searchByName(string $nameFragment, string $language = 'de'): array;


    /**
     * Uebertraegt die wichtigsten Eigenschaften eines Landes- 
     *  (Staats)Datensatzes in ein Objekt einer Standardklasse und gibt 
     *  dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass;


    /**
     * Bestimmt den 2-Buchstabencode eines Landes (Staats).
     * 
     * @param string $identifier Die Kennung nach der in der Datenbak gesucht 
     *  wird, kann eine 2- oder 3-Buchstabenkennung in Grossbuchstaben oder 
     *  ein Namensfragment sein.
     * @return string 2-Buchstabenkennung in Grossbuchstaben.
     */
    public function determine2LetterCode(string $identifier): string;


    /**
     * Setzt ausgewaehlte Landes- (Staats-)Eigenschaften auf vordefinierte 
     *  Werte zurueck.
     *
     * @return CountryModel
     */
    public function resetProperties(): CountryModel;
}