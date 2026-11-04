<?php


/**
 * php/modules/interface/countries.php
 *
 * Countries Interface - Interface des Modells der Laender (Staates) (plural).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Countries Interface - Interface des Modells der Laender (Staates) (plural).
 */
interface CountriesInterface
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
     * Holt eine Liste aller Identifikationsnummern von Landesdatensaetzen 
     *  in der Datenbank. 
     *  
     * @return array $idList Liste aller Identifikationsnummern.
     */
    public function getIdList(): array;


    /**
     * Holt eine List bestehend aus Standardobjekten bestehemd aus 
     *  Laenderdaten.
     *  
     * @return array $stdObjList Liste aus Standardobjekten.
     */
    public function getListOfStdObjects(): array;
}