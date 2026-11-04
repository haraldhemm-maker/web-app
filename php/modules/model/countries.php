<?php


/**
 * php/modules/model/countries.php
 *
 * Countries Model - Modell der Laender (Staaten) (plural).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use \HaHeLa\CountryModel as Country;


/**
 * Countries Model - Modell der Laender (Staaten) (plural).
 */
class CountriesModel implements CountriesInterface
{
    /**
     * Datenbank-Handle
     * @property \PDO
     */
    private \PDO $dbh;
    
    /**
     * Konfigurationsdaten des Systems.
     * @property array
     */
    private array $config;    
        
    /**
     * Konfiguration- oder Suchoptionen.
     * @property array
     */
    protected array $options = array();
	
	/**
	 * 2-Grossbuchstabenkennungen der Laender, die einer bevorzugten
	 * 	Verarbeitung unterliegen als Schluessel und Identifikationsnummern
	 * 	der entsprechenden Datenbankdatensaetze als Werte.
	 * @property array
	 */
	protected array $preferred_countries;
    
    
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     */
    public function __construct(\PDO $dbh, array $config, array $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
		// Initialisierung bevorzugter Laender. Nach der Initialisierung 
		// 	ist das Feld $preferred_countries mit Schluessel-Wert-Paaten 
		// 	gefuellt. Schluessel sind die 2-Grossbuchstabenkennungen der 
		// 	Laender und Werte sind die entsprecheden Identifikationsnummern 
		// 	der Datenbankdatensaetze.
		$this->preferred_countries = array_fill_keys($this->config['COUNTRY']['PREFERED'], 0);
		$country = new Country($this->dbh, $this->config, $this->options);
		foreach ($this->preferred_countries as $preferredCountryKey => $preferredCountryValue) {
			$this->options = array();
			$this->options['alpha_2'] = $preferredCountryKey;
			$this->preferred_countries[$preferredCountryKey] = $country
			->setOptions($this->options)
			->exists();
		}
    }
    
    
    /**
     * Holt eine Liste aller Identifikationsnummern von Landesdatensaetzen 
     *  in der Datenbank. 
     *  
     * @return array $idList Liste aller Identifikationsnummern.
     */
    public function getIdList(): array
    {
        $query  = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country";
        $query .= ' ORDER BY name_de ASC';
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $idList = array();
		$preferredIdList = array();
		$haystack = array_column($this->preferred_countries, NULL);
		while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
		    $id = intval($row['id']);
			if (in_array($id, $haystack)) {
				$preferredIdList[] = $id;
			}
			else {
				$idList[] = $id;
			}
		}
		$idList = array_merge($preferredIdList, $idList);
        return $idList;
    }
    
    
    /**
     * Holt eine List bestehend aus Standardobjekten bestehemd aus 
     *  Laenderdaten.
     *  
     * @return array $stdObjList Liste aus Standardobjekten.
     */
    public function getListOfStdObjects(): array 
    {
        $ids = $this->getIdList();
        $country = new Country($this->dbh, $this->config, $this->options);
        $stdObjList = array();
        foreach ($ids as $id) {
            $stdObj = $country->setId($id)->read()->toStdObject();
            $stdObjList[] = $stdObj;
        }
        return $stdObjList;
    }
    
    
    /************************************** private und geschuetzte Methoden */
}
