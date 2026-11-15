<?php


/**
 * php/modules/interface/driver/openfigi.php
 *
 * Open FIGI Interface - Interface zum Model der Globalen Identifikatoren fuer 
 *  Finanzinstrumente (Open Financial Instrument Global Identifier OpenFIFI) 
 *  (singular). 
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Open FIGI Interface - Interface zum Model der Globalen Identifikatoren fuer 
 *  Finanzinstrumente (Open Financial Instrument Global Identifier OpenFIFI) 
 *  (singular). 
 */
interface DriverOpenfigiInterface
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
     * Ermittelt zu einer ISIN die globalen Identifikatoren fuer 
     *  Finanzinstrumente (Open Financial Instrument Global Identifier - 
     *  OpenFIFI).
     * 
     * @param string $isin ISIN, International Securities Identification Number
     * @param string $code Optional: Schluessel/Wert-Paar; Schluessel ist 
     *  'exchCode' oder 'micCode'. Wert ist ein Bloomberg-eigenes 
     *  Boersen-Kuerzel, der MIC oder NULL.
     * @return array[] Liste von Treffern, jeder Treffer wiederum als Array:
     *  array(8) { // Beispiel fuer die ISIN US5949181045
     *      ["figi"]=>string(12) "BBG000BPH459"
     *      ["ticker"]=>string(4) "MSFT"
     *      ["exchCode"]=>string(2) "US" - "Bloomberg exchange mnemonic" 
     *      ["securityType"]=>string(12) "Common Stock"
     *      ["securityType2"]=>string(12) "Common Stock"
     *      ["marketSector"]=>string(6) "Equity"
     *      ["name"]=>string(14) "MICROSOFT CORP"
     *      ["compositeFIGI"]=>string(12) "BBG000BPH459"
     * }
     * @note *** Hinweise: ***
     *  - Eine ISIN → mehrere Treffer: OpenFIGI liefert pro Handelsplatz einen 
     *      Eintrag (Zum Beispiel notiert Siemens an vielen Handelsplaetzen). 
     */
    public function figiByIsin(string $isin, ?string $code = null): array;
    
    
    /*
     * Setzt spezifische Objekteigenschaften.
     * 
     * @param array $hit Feld mit folgenden Eintraegen:
     *  array(8) { // z.B. Microsoft 
     *      ["figi"]=>string(12) "BBG000BPH459"
     *      ["ticker"]=>string(4) "MSFT"
     *      ["exchCode"]=>string(2) "US" - ist der Bloomberg-Composite-Code
     *      ["securityType"]=>string(12) "Common Stock"
     *      ["securityType2"]=>string(12) "Common Stock"
     *      ["marketSector"]=>string(6) "Equity"
     *      ["name"]=>string(14) "MICROSOFT CORP"
     *      ["compositeFIGI"]=>string(12) "BBG000BPH459"
     *  }
     * @return DriverOpenfigiModel
     */
    public function setProperties(array $hit): DriverOpenfigiModel;
    
    
    /**
     * 'protected' Objekteigenschaften werden auf ihre voreingestellten Werte 
     * zurueckgesetzt.
     * 
     * @param string $visibilityModifier Optional: Sichtbarkeitsmodifikator 
     *  kann folgende Werte annehmen: 'public', 'protected', 'private'. 
     *  Voreingestellter Wert ist 'protected'.
     * @return DriverOpenfigiModel
     */
    public function resetProperties($visibilityModifier = 'protected'): DriverOpenfigiModel;
    
    
    /**
     * Zeichenkette in der snake-Schreibweise in die camel-case-Schreibweise 
     *  umwandeln.
     * 
     * @param string $snake Zeichenkette in der snake-Schreibweise 
     *  z.B. 'eins_zwei_drei'
     * @return string Zeichenkette in der camel-case-Schreibweise
     *  z.B. 'EinsZweiDrei'
     */
    public function snakeToCamelCase(string $snake): string;
    
    
    /** 
     * Zeichenkette in der camel-case-Schreibweise in die snake-Schreibweise 
     *  umwandeln.
     * 
     * @param string $camelCase Zeichenkette in der camel-case-Schreibweise
     *  z.B. 'EinsZweiDrei', 'EinsZwei2'
     * @return string Zeichenkette in der snake-Schreibweise 
     *  z.B. 'eins_zwei_drei', 'eins_zwei_2'
     */    
    public function camelCaseToSnake(string $camelCase): string;
}

