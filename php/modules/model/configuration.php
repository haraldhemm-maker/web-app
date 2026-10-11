<?php


/**
 * php/modules/model/configuration.php
 *
 * Configuration Model - Model zur Konfiguration des PHP-Systems.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Configuration Model - Model zur Konfiguration des PHP-Systems.
 */
class ConfigurationModel implements ConfigurationInterface
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
	const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';

    
    /**
     * Vertrauliche Sektionen in der Konfigurationsdatei
     * @property array confidentialSections
     */
    private $confidentialSections = array(
        'DATABASE'
    );
	
	/**
	 * Betriebssystemname
	 * @var string $osName 
	 * Beispiele: 
	 * 	- 'Linux'
	 */
	private $osName = '';
	
	/**
	 * Vollqualifizierter Dateiname der Konfigurationsdatei.
	 * @property string $fileName 
	 */
	private $fileName = '';

    /**
     * Gesamtkonfiguration
     * @var array
     */
    private $config = array();


    /**
     * Constructor
     *
     * @return ConfigurationModel
     */
    public function __construct ()
    {
		$this->osName = php_uname('s');
		$this->fileName = str_replace('*', strtolower($this->osName), self::CONFIGURATION_BASEFILE_NAME_TEMPLATE);
        $this->fileName = realpath(__DIR__.DS.self::CONFIGURATION_PATH) . DS . $this->fileName;
    }
	
	
	/**
	 * Holt den Betriebssystemname
	 * 
	 * @return string Betriebssystemname; z.B. 'Linux'.
	 */
	public function getOsName (): string
	{
		return strval($this->osName);
	}


    /**
     * Liest die Konfiguration und gibt sie als assoziatives Array zurueck.
     *
     * @param bool $notConfidential Wenn dieser Parameter TRUE ist, dann 
     *  werden KEINE vertraulichen Konfigurationsdaten zurueckgegeben. Ist 
     *  dieser Parameter FALSE, dann werden auch vertrauliche Daten zurueck- 
     *  gegeben.
     * @return array
     */
    public function read ($notConfidential = TRUE): array
    {
        $this->config = parse_ini_file($this->fileName, true);
        if ($notConfidential) {
            foreach ($this->confidentialSections as $section) {
                unset($this->config[$section]);
            }            
        }
        return $this->config;
    }
    
    
    /**
     * Schreibt die Konfigurationdatei.
     * 
     * @param array $data
     * @param boolean $hasSections
     * @return boolean
     */
    public function write_ini_file (array $data, bool $hasSections = true): bool
	{
		$content = '';
		foreach ($data as $section => $values) {
			if ($hasSections) {
				$content .= "[$section]\n";
				foreach ($values as $key => $value) {
					if (is_array($value)) {
						$lastIndex = count($value) - 1;
						foreach ($value as $index => $item) {
							if ($index === $lastIndex)
								$content .= "{$key}[] = " . $this->formatIniValue($item);
							else 
								$content .= "{$key}[] = " . $this->formatIniValue($item) . "\n";
						}
					} 
					else {
						$content .= "$key = " . $this->formatIniValue($value);
					}
			        $content .= "\n";
			    }
			}
			else {
				$content .= "$section = " . $this->formatIniValue($value);
			}
		}
		return file_put_contents($this->fileName, $content) !== FALSE;
	}
	
    
  /* ********************************************** private (Hilfs-)Methoden */

    
    private function formatIniValue ($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false'; // Boolean
        }
        if (is_numeric($value)) { // Integer | Float
            return $value;
        }
        return '"' . addslashes($value) . '"'; // String
    }
}
