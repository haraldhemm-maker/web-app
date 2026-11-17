<?php
/**
 * php/modules/interface/lei.php
 *
 * Lei Interface - Interface des Modells eines Legal-Entity-Identifiers 
 *  (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2020-2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */
namespace HaHeLa;


/**
 * Lei Interface - Interface des Modells eines Legal-Entity-Identifiers 
 *  (singular).
 */
interface LeiInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options (optional) Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer eines
     *  LEI in der Datenbank. Wenn dieser Parameter vorhanden ist, 
     *  dann werden die Daten des entsprechenden LEIs aus der Datenbank 
     *  gelesen und die Objekteigenschaften entsprechend initialisiert. 
     */
    public function __construct(\PDO $dbh, array $config, array $options = array());


    /**
     * Holt die Identifikationsnummer des LEIs in der Datenbank.
     *
     * @return integer Identifikationsnummer des LEIs.
     */
    public function getId(): int;


    /**
     * Setzt die Identifikationsnummer des LEIs.
     *
     * @param integer $id Identifikationsnummer des LEIs.
     * @return \HaHeLa\Modules_Model_Lei
     */
    public function setId(int $id): \HaHeLa\LeiModel;


    /**
     * Holt die Optionen
     *
     * @return array Optionen
     */
    public function getOptions(): array;


    /**
     * Setzt die Optionen
     *
     * @param array $options Optionen
     * @return \HaHeLa\LeiModel
     */
    public function setOptions(array $options): \HaHeLa\LeiModel;


    /**
     * Holt die zusaetzlichen Objekteigenschaften, d.h. Objekteigenschaffen exklusive 
     *  $id und $options.
     *
     * @return array
     */
    public function getProperties(): array;


    /**
     * Setzt die zusaetzlichen Objekteigenschaften. $id und $options zaehlen 
     *  nicht zu den zusaetzlichen Objekteigenschaften.
     *
     * @param array $properties Zusaetzliche Objekteigenschaften.
     * @return \HaHeLa\Modules_Model_Lei
     */
    public function setProperties(array $properties): \HaHeLa\LeiModel;


    /**
     * Holt den Wert einer Eigenschaft.
     * 
     * @param string $propName Name einer Objekteigenschaft. Der Name der 
     *  Objekteigenschaft entspricht dem entsprechenen Tabellenspaltennamen 
     *  der Tabelle 'wa_lei'.
     * @return string Wert einer Eigenshaft.
     */
    public function getProperty(string $propName): string;


    /**
     * Setzt den Wert einer Eigenschaft.
     * 
     * @param string $propName Name einer Objekteigenschaft. Der Name der 
     *  Objekteigenschaft entspricht dem entsprechenen Tabellenspaltennamen 
     *  der Tabelle 'tv_lei'.
     * @param string $propValue Wert einer Objekteigenschaft.
     * @return \HaHeLa\LeiModel
     */
    public function setProperty(string $propName, string $propValue): \HaHeLa\LeiModel;


    /**
     * Erzeugt einen LEI-Datensatzes in der Datenbank.
     * 
     * @return integer Identifikationsnummer des neuen LEI-Datensatzes 
     *  in der Datenbank oder Wert 0 als Hinweis, dass kein LEI-Datensatz 
     *  in der Datenbank erzeugt werden konnte.
     */
    public function create(): int;


    /**
     * Liest den LEI-Datensatz, der durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert wird. 
     *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
     *  der Datenbank vorhanden, dann werden die Eigenschaften des
     *  LEI-Objekts mit vorgegebenen Werten gefuellt.
     *
     * @return \HaHeLa\LeiModel Gibt das Objekt selbst zurueck 
     *  (fluent interface).
     */
    public function read(): \HaHeLa\LeiModel;


    /**
     * Erneuert einen LEI-Datensatz in der Datenbank. Der zu erneuerten 
     *  Datensatz ist durch die Identifikationsnummer in der Eigenschaft 
     *  $id repraesentiert. Die zu erneuernten Daten befinden sich in der 
     *  Eigenschaft $properties. 
     * 
     * @return bool Ergebnis der Aktualisierung. TRUE - die Aktualisierung
     *  war erfolgreich. FALSE - der Datensatz wurde nicht aktualisert.
     */
    public function update(): bool;


    /**
     * Loescht einen LEI-Datensatz aus der Datenbank und setzt die 
     *  ausgewaehlten Objekteigenschaften auf Standardwerte zurueck.
     *  Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert.
     *
     * @return bool Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete(): bool;


    /**
     * Setzt ausgewaehlte, proteced Eigenschaften eines Objektes der Instanz 
     *  der LEI-Klasse auf vordefinierte Werte zurueck.
     *
     * @param bool $additionalOnly Optional Wenn dieser Indikator TRUE 
     *  ist, dann dann werden nur die zusaetzlichen Eigenschaften auf 
     *  vordefinierte Werte zurueck gesetzt. Wenn dieser Indikator, 
     *  wie voreingestellt FALSE ist, dann werden alle ausgewaehlten.
     *  protected Eigenschaften auf vordefinierte Werte eingestellt.
     * @return HaHeLa\LeiModel
     */
    public function resetProperties(bool $additionalOnly = FALSE): \HaHeLa\LeiModel;


    /**
     * Such LEI in der Datenbank. 
     * 
     * @param string $lei LEI
     * @return bool Wenn der LEI in der Datenbank gefunden wird, dann werden 
     *  die Eigenschaften des LEI-Objektes entsprechend initialisiert und 
     *  die Methode liefert den Wert TRUE. Wenn der LEI nicht in der 
     *  Datenbank gefunden wird, dann werden die Eigenschaften des 
     *  LEI-Objektes auf voreingestellte Werte zurueckgesetzt und die 
     *  Methode liefert den Wert FALSE.
     */
    public function searchLei(string $lei): bool;

        
    /**
     * Ueberprueft ob LEI-Datensaetze mit den gegebenen Eigenschaften in der 
     *  Datenbank existieren.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *  als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *  Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *  Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return array|FALSE Wenn LEI-Datensaetze gefunden werden, dann werden 
     *  deren Identifikationsnummern zurueckgegeben, sonst FALSE.
     */
    public function exists(): array|false;


    /**
     * Uebertraegt die wichtigsten Eigenschaften eines LEI-Datensatzes 
     *     in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass;


    /**
     * Prueft die Syntax des LEIs.
     * 
     * @param string $lei Vollstaendiger 20-stelliger LEI.
     * @return bool Wenn der LEI eine gueltige Syntax hat, dann liefert die 
     *  Methode den boolschen Wert TRUE, sonst FALSE.
     */
    public function validateLeiSyntax(string $lei): bool;
}
