<?php


/**
 * php/modules/interface/currencies.php
 *
 * Currencies Interface - Interface des Modells der Waehrungen (plural).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hem@m Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Currencies Interface - Interface des Modells der Waehrungen (plural).
 */
interface CurrenciesInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     */
    public function __construct(\PDO $dbh, array $config, array $options = array());


    /**
     * Holt eine Liste aller Identifikationsnummern von Waehrungsdatensaetzen 
     *  in der Datenbank. Je nach Inhalt des Eigenschaftfeldes 
     *  $preferred_currencies, werden die Waehrungen in diesem Feld an den 
     *  Anfang der Ergebnisliste gestellt. Die restlichen Eintraege in der 
     *  Ergebnisliste sind nach dem englischen Waehrungsnamen sortiert.
     *  
     * @return array $idList Liste aller Identifikationsnummern.
     */
    public function getIdList(): array;



    /**
     * Holt eine Liste bestehend aus Standardobjekten von Waehrungsdaten.
     *  
     * @return array $stdObjList Liste aus Standardobjekten mit 
     *  Waehrungsdaten.
     */
    public function getListOfStdObjects(): array;
}