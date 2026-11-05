<?php


/**
 * php/modules/interface/security/type.php
 *
 * Security Type Interface - Interface des Modells eines Wertpapiertyps (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Security Type Interface - Interface des Modells eines Wertpapiertpys (singular).
 */
interface SecurityTypeInterface
{
    /**
        * Konstruktor
        *
        * @param \PDO $dbh Datenbank-Handle.
        * @param array $config Konfigurationsdaten.
        * @param array $options Optionale Konfigurationsparameter.
        *
        * @param int $id (optional) Identifikationsnummer eines
        *   Wertpapiertypdatensatzes in der Datenbank. Wenn dieser Parameter
        *   vorhanden ist, dann werden die Daten des entsprechenden
        *   Wertpapiertyps aus der Datenbank gelesen und die Objekteigenschaften
        *   entsprechend initialisiert.
        */
       public function __construct (\PDO $dbh, array $config, array $options = array());
       
       
       /**
        * Liest den Wertpapiertypdatensatz, der durch die Identifikationsnummer
        *   in der Eigenschaft $id repraesentiert wird. Ist ein Datensatz
        *   mit der gegebenen Identifikationsnummer nicht in der Datenbank
        *   vorhanden, dann werden die Eigenschaften des Wertpapiertypobjekts
        *   mit vorgegebenen Werten gefuellt.
        *
        * @return SecurityTypeModel Gibt das Objekt selbst zurueck
        *   (fluent interface).
        */
       public function read (): SecurityTypeModel;
       
       
       /**
        * Setzt ausgewaehlte Wertpapiertypeigenschaften auf vordefinierte 
        *  Werte zurueck.
        *
        * @return HaHeLa\SecurityTypeModel
        */
       public function resetProperties (): SecurityTypeModel;
       
       
       /**
        * Uebertraegt die wichtigsten Eigenschaften eines 
        *  Wertpapiertypdatensatzes in ein Objekt einer Standardklasse 
        *  und gibt dieses Objekt zurueck.
        *
        * @return \stdClass
        */
       public function toStdObject (): \stdClass;
       
       
       /**
        * Ueberprueft, ob ein Wertpapiertyp bereits in der Datenbank existiert.
        *
        * @property array $options Assoziatives Feld (Map) mit den Suchoptionen
        *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
        *   Die Bezeichnung der Schluessel muss mit dem entsprechenden
        *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
        * @return int|false Wenn der Wertpapiertyp gefunden wird, dann wird 
        *  dessen Identifikationsnummer zurueckgegeben, sonst FALSE.
        */
       public function exists (): int|false;
}