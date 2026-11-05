<?php


/**
 * php/modules/interface/security/types.php
 *
 * Security Types Interface - Interface des Modells der Wertpapiertypen (plural).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Security Types Interface - Interface des Modells der Wertpapiertypen (plural).
 */
interface SecurityTypesInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionen.
     */
    public function __construct(\PDO $dbh, array $config, array $options = array());


    /**
     * Holt eine Liste aller Wertpapiertypen als Objekte der Standardklasse.
     * 
     * @return array Ein indexiertes Feld mit Objekten der Standardklasse 
     *  die Wertpapiertypdaten enthaelt.
     */
    public function getStdObjList(): array;
}