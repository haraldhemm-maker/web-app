<?php


/**
 * php/modules/interface/driver/general.php
 *
 * DriverGeneralInterface - Allgemeines Interface der Datenquellentreiber. 
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * DriverGeneralInterface - Allgemeines Interface der Datenquellentreiber. 
 */
interface DriverGeneralInterface
{
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optional Optionen.
     */
    public function __construct(\PDO $dbh, array $config, array $options = array());
    
    
	/**
	 * Holt die Version.
	 * 
	 * @return string Version
	 */
	public function getVersion(): string;


	/**
	 * Holt den Namen der Datenquelle.
	 * 
	 * @return string Name der Datenquelle.
	 */
	public function getName(): string;


	/**
	 * Holt die Beschreibung der Datenquelle.
	 * 
	 * @return string Beschreibung der Datenquelle.
	 */
	public function getDescription(): string;


	/**
	 * Holt die Domaene der Datenquelle.
	 * 
	 * @return string Domaene der Datenquelle.
	 */
	public function getDomain(): string;


	/**
	 * Holt den URL der Website.
	 * 
	 * @return string
	 */
	public function getUrl(): string;
    
    
    /**
     * Holt, mit der Angabe der ISIN, die Wertpapierdaten.
     * 
     * @param string $isin Internationale Wertpapierkennnummer.
     * @return Assoziatives Array (Map) mit den folgenden Wertpapierdaten:
     *  array(
     *      int:    type_id,        // Fremdschluessel (FK) auf wa_security_type
     *      string: name,           // Name des Wertpapiers
     *      string: isin,           // Internationale Wertpapierkennnummer
     *      string: ticker,         // Symbol des Wertpapers
     *      string: country,        // Alpha-2-Code des Landes
     *      string: currency,       // Waehrungsabkuerzung
     *      string: industry,       // Branche (uebergeordnet)
     *      string: mic             // Handelsplatzkennzeichnung
     *  )
     */
    public function getSecurityDataByIsin(string $isin): array;
}

