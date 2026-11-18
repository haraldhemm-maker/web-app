<?php


/**
 * php/modules/interface/market/categories.php
 *
 * Market Categories Interface - Interface des Modells von Handelsplatzkategorien (plural).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Market Categories Interface - Interface des Modells von Handelsplatzkategorien (plural).
 */
interface MarketCategoriesInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options (optional) Optionale Konfigurationsparameter.
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
     * Holt eine Liste aller Identifikationsnummern von Markplatzkategorien
     *  in der Datenbank.
     *  
     * @return array $idList Indexierte Liste aller Identifikationsnummern.
     */
    public function getIdList (): array;


    /**
     * Holt eine List bestehend aus Standardobjekten von Markplatzkategorien.
     *
     * @return array $stdObjList Indexierte Liste aus Standardobjekten.
     */
    public function getListOfStdObjects (): array;
}