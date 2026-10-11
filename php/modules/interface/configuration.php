<?php


/**
 * php/modules/interface/configuration.php
 *
 * Configuration Interface - Interface zur Konfiguration des PHP-Systems.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Configuration Interface - Interface zur Konfiguration des PHP-Systems.
 */
interface ConfigurationInterface
{
    /**
     * Constructor
     *
     * @return ConfigurationModel
     */
    public function __construct ();

    
    /**
     * Holt den Betriebssystemname
     * 
     * @return string Betriebssystemname; z.B. 'Linux'.
     */
    public function getOsName (): string;


    /**
     * Liest die Konfiguration und gibt sie als assoziatives Array zurueck.
     *
     * @return array
     */
    public function read (): array;
    
    
    /**
     * Schreibt die Konfigurationdatei.
     * 
     * @param array $data
     * @param boolean $hasSections
     * @return boolean
     */
    public function write_ini_file (array $data, bool $hasSections = true): bool;
}
