<?php


/**
 * php/modules/model/market/categories.php
 *
 * Market Categories Model - Modell von Handelplatzkategorien (pluaral).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use \HaHeLa\MarketCategoryModel as MarketCategory;


/**
 * Market Categories Model - Modell von Handelplatzkategorien (pluaral).
 */
class MarketCategoriesModel implements MarketCategoriesInterface
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
	 * Konstruktor
	 *
	 * @param \PDO $dbh Datenbank-Handle.
	 * @param array $config Konfigurationsdaten.
	 * @param array $options (optional) Optionale Konfigurationsparameter.
	 */
	public function __construct (\PDO $dbh, array $config, array $options = array())
	{
		$this->dbh = $dbh;
		$this->config = $config;
		$this->options = $options;
	}
	
	
	/**
	 * Holt die Optionen
	 *
	 * @return array Optionen
	 */
	public function getOptions (): array
	{
		return $this->options;
	}
	
	
	/**
	 * Setzt die Optionen
	 *
	 * @param array $options Optionen
	 * @return \HaHeLa\MarketCategoryModel
	 */
	public function setOptions (array $options): \HaHeLa\MarketCategoryModel
	{
		$this->options = $options;
		return $this;
	}
	
	
	/**
	 * Holt eine Liste aller Identifikationsnummern von Markplatzkategorien
	 *  in der Datenbank.
	 *  
	 * @return array $idList Indexierte Liste aller Identifikationsnummern.
	 */
	public function getIdList (): array
	{
		$query  = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}market_category";
		$query .= ' ORDER BY code ASC';
		$stmt = $this->dbh->prepare($query);
		$stmt->execute();
		$idList = array();
		while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			$idList[] = intval($row['id']);
		}
		return $idList;
	}
	
	
	/**
	 * Holt eine List bestehend aus Standardobjekten von Markplatzkategorien.
	 *
	 * @return array $stdObjList Indexierte Liste aus Standardobjekten.
	 */
	public function getListOfStdObjects (): array
	{
		$ids = $this->getIdList();
		$marketCategory = new MarketCategory($this->dbh, $this->config, $this->options);
		$stdObjList = array();
		foreach ($ids as $id) {
			$stdObjList[] = $marketCategory->setId($id)->read()->toStdObject();
		}
		return $stdObjList;
	}
	
	
	/************************************* private und geschuetzte Methoden */
	
	
}
