<?php


/**
 * php/modules/interface/user.php
 *
 * User Interface - Interface des Benutzer-Modells.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * User Interface - Interface des Benutzer-Modells.
 */
interface UserInterface
{
    /**
     * Constructor
     *
     * @param resource $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options optional Optionen
     *
     * @param integer $id (optional) Identifikationsnummer eines
     *   User-Datensatzes in der Datenbank.
     *
     * @return UserModel
     */
    public function __construct ($dbh, $config, $options = array());


    /**
     * Setzt die Identifikationsnummer des Benutzerkontos.
     * 
     * @param int $id Identifikationsnummer.
     * @return UserModel
     */
    public function setId(int $id): UserModel;


    /**
     * Setzt die Identifikationsnummer (Subject) vom OAuth2-Server.
     * 
     * @param int $id Subject/Identifikationsnummer.
     * @return UserModel
     */
    public function setSub(int $sub): UserModel;


    /**
     * Setzt den Vornamen des Benutzers.
     * 
     * @param stirng $firstName Vorname des Benutzers.
     * @return UserModel
     */
    public function setFirstName(string $firstName): UserModel;


    /**
     * Setzt den Nachnamen des Benutzers.
     * 
     * @param string $lastName Nachname des Benutzers.
     * @return UserModel
     */
    public function setLastName(string $lastName): UserModel;


    /**
     * Holt Informationen ueber den aktuellen Benutzer aus der Datenbank des 
     *  OAuth-Servers.
     * 
     * @return array Assoziatives Array mit den folgenden Informationen 
     *  ueber den Benutzer in der Datenbank des OAuth-Servers.
     */
    public function getUserInfoFromOAuthServer(): array;


    /**
     * Ueberprueft, ob ein Benutzer mit den Eigenschaften im $criteria-Array
     *  in der lokalen Datenbank existiert. Die Eigenschaften im $criteria-
     *  Array werden bei der Suche in der lokalen Datenbank UND-verknuepft.
     *  Die ID des gefundenen Benutzers wird als einziger Eintrag in einem 
     *  Array zurueckgegeben. Wenn mehr als ein Benutzer mit den Eigenschaften 
     *  ermittelt wird, dann werden alle IDs dieser Benutzer in einem Array
     *  zuruckgegeben. Wenn KEIN Benutzer mit den Eigenschaften gefunden wird, 
     *  dann wird ein leeres Array zurueckgegeben.
     * 
     * @param array $criteria Ein Array mit den Eigenschaften mit denen der 
     *  Benutzer in der lokalen Datenbank gesucht wird.
     * @return Array Ein Array mit den IDs der gefundenen Benutzer.
     * @throws \InvalidArgumentException Wenn nach einer Eigenschaft in einer 
     *  nicht erlaubten Spalte gesucht wird, dann wird eine Exception 
     *  ausgeloest.
     */
    public function exists(array $criteria): array;


    /**
     * Erzeugt einen Benutzekonto-Datensatzes in der lokalen Datenbank.
     * 
     * @return int Identifikationsnummer des neuen 
     *  Benutzerkonto-Datensatzes in der lokalen Datenbank.
     */
    public function create(): int;


    /**
     * Liest den User-Datensatz, der durch die Identifikationsnummer in der
     *   Eigenschaft $id repraesentiert wird.
     *
     * @return UserModel Gibt das Objekt selbst zurueck 
     *  (fluent interface).
     */
    public function read(): UserModel;


    /**
     * Aktualisiert den Benutzerkonto-Datensatz in der lokalen Datenbank. 
     * 
     * @return bool Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update(): bool;
    
    
    /**
     * Loescht einen Benutzerkonto-Datensatz. 
     *   Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *   in der Eigenschaft $id repraesentiert.
     *
     * @return bool Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete (): bool;
    
    
    /**
     * Uebertraegt die wichtigsten Eigenschaften eines lokalen Benutzerkontos 
     *  in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass;


    /**
     * Uebertraegt die wichtigsten Eigenschaften eines lokalen Benutzerkontos 
     *  in ein assoziatives Array und gibt dieses Array zurueck.
     *
     * @return array
     */
    public function toArray(): array;
}
