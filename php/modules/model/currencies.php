<?php


/**
 * php/modules/model/currencies.php
 *
 * Currencies Model - Modell der Waehrungen (plural).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use \HaHeLa\CurrencyModel as Currency;


/**
 * Currencies Model - Modell der Waehrungen (plural).
 */
class CurrenciesModel implements CurrenciesInterface
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
    protected array $options;
	
	/**
	 * 3-Grossbuchstabenkennungen von Waehrungen die einer bevorzugten
	 * 	Verarbeitung unterliegen.
	 * @property array
	 */
	protected array $preferred_currencies;
    
	
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
		// Initialisierung bevorzugter Waehrungen.
		$this->preferred_currencies = array_fill_keys($this->config['CURRENCY']['PREFERED'], 0);
		$currency = new Currency($this->dbh, $this->config, $this->options);
		foreach ($this->preferred_currencies as $preferredCurrenyKey => $preferredCurrenyValue) {
			$this->options = array();
			$this->options['currency'] = $preferredCurrenyKey;
			$this->preferred_currencies[$preferredCurrenyKey] = $currency
			->setOptions($this->options)
			->exists();
		}
    }
    
    
    /**
     * Holt eine Liste aller Identifikationsnummern von Waehrungsdatensaetzen 
     *  in der Datenbank. Je nach Inhalt des Eigenschaftfeldes 
	 * 	$preferred_currencies, werden die Waehrungen in diesem Feld an den 
	 * 	Anfang der Ergebnisliste gestellt. Die restlichen Eintraege in der 
	 * 	Ergebnisliste sind nach dem englischen Waehrungsnamen sortiert.
     *  
     * @return array $idList Liste aller Identifikationsnummern.
     */
    public function getIdList(): array
    {
        $query  = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency";
        $query .= ' ORDER BY name_en ASC';
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $idList = array();
		$preferredIdList = array();
		$haystack = array_column($this->preferred_currencies, NULL);
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
     * Holt eine Liste bestehend aus Standardobjekten von Waehrungsdaten.
     *  
     * @return array $stdObjList Liste aus Standardobjekten mit 
     *  Waehrungsdaten.
     */
    public function getListOfStdObjects(): array
    {
        $ids = $this->getIdList();
        $currency = new Currency($this->dbh, $this->config, $this->options);
        $stdObjList = array();
        foreach ($ids as $id) {
            $stdObj = $currency->setId($id)->read()->toStdObject();
            $stdObjList[] = $stdObj;
        }
        return $stdObjList;
    }
    
    
    /* *********************************** private und geschuetzte Methoden */
    
    
}
