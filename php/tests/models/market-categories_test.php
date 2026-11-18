<?php


/**
 * PHP unit tests of the HaHeLa\MarketCategoriesModel class 
 *  - Model der Handelplatzkategorien.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd ~harald/public_html/app/tv-widget/php/tests
 * > php phpunit-9.6.13.phar --configuration market-categories.xml
 */


declare(strict_types=1);


use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'market'.DS.'category.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'market'.DS.'categories.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'market'.DS.'category.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'market'.DS.'categories.php';


/**
 * FileModel Test
 */
class MarketCategoriesModelTest extends TestCase
{
	/**
	 * Konfiguration.
	 * @var array
	 */
	public array $config;
	
	/**
	 * Datenbank(server)-Handle.
	 * @var \PDO
	 */
	public \PDO $dbh;
	
	/**
	 * Ergebnis der Aktionen mit dem Datenbankserver eine Verbindung
	 *   herzustellen und die Datenbank zu oeffnen.
	 * @var \stdClass
     */
	public $dbhResult;
	
	/**
	 * Optionen - zur Zeit unbenutzt.
	 * @var array
     */
	public array $options = array();
	
	
	/**
	 * Setup of the PHP unit test.
	 *
	 * @return void
	 */
	protected function setUp (): void
	{
        // Konfiguration laden.
        $osName = php_uname('s');
        $fileName = str_replace('*', strtolower($osName), self::CONFIGURATION_BASEFILE_NAME_TEMPLATE);
        $fileName = realpath(__DIR__.DS.self::CONFIGURATION_PATH) . DS . $fileName;
        $this->config = parse_ini_file($fileName, true);
		
		// Verbindung mit Datenbankserver herstellen und Datenbank oeffnen.
		$this->dbhResult = new stdClass();
		$dataSourceName = "mysql:host={$this->config['database']['db_servername']};dbname={$this->config['database']['db_databasename']}";
		try {
			$this->dbh = new PDO($dataSourceName, $this->config['database']['db_username'], $this->config['database']['db_password']);
			// set the PDO error mode to exception
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbhResult->result = true;
			$this->dbhResult->code = 0;
			$this->dbhResult->message = 'Eine Verbindung zum Datenbankserver und zur Datenbank konnte hergestellt werden. ';
		}
		catch(PDOException $e) {
			$this->dbhResult->result = false;
			$this->dbhResult->code = $e->getCode();
			$this->dbhResult->message = 'Eine Verbindung zum Datenbankserver bzw. zur Datenbank konnte NICHT hergestellt werden: ' . $e->getMessage();
		}
		if (false === $this->dbhResult->result) {
			die($this->dbhResult->message . $this->dbhResult->code);
		}
	}
	
	
	/**
	 * Erzeuge eine Instanz der Klasse \HaHeLa\MarketCategoriesModel.
	 */
	public function testCreateMarketCategoriesObject ()
	{
		// Zwei Pflichtparameter und ein optionaler Parameter.
		$model = new \HaHeLa\MarketCategoriesModel($this->dbh, $this->config, $this->options);
		$this->assertTrue($model instanceof \HaHeLa\MarketCategoriesModel);
		$this->assertInstanceOf('\HaHeLa\MarketCategoriesModel', $model);
	}
	
	
	/**
	 * Holt eine indexierte Liste von Identifikationsnummern aller 
	 * 	Marktplatzkategorien in der Datenbank.
	 */
	public function testGetIdList ()
	{
		$model = new \HaHeLa\MarketCategoriesModel($this->dbh, $this->config, $this->options);
		$ids = $model->getIdList();
		$this->assertIsArray($ids);
		$this->assertNotEmpty($ids);
		foreach ($ids as $id) {
			$this->assertNotEmpty($id);
			$this->assertIsInt($id);
		}
	}
	
	
	/**
	 * Holt eine indexierte Liste von Standardobjekten aller 
	 * 	Marktplatzkategorien in der Datenbank.
	 */
	public function testGetListOfStdObjects ()
	{
		$model = new \HaHeLa\MarketCategoriesModel($this->dbh, $this->config, $this->options);
		$objects = $model->getListOfStdObjects();
		$this->assertIsArray($objects);
		$this->assertNotEmpty($objects);
		foreach ($objects as $object) {
			$this->assertNotEmpty($object);
			$this->assertIsObject($object);
			$this->assertIsInt($object->id);
			$this->assertIsString($object->code);
			$this->assertIsArray($object->name);
			$this->assertIsString($object->name['de']);
			$this->assertIsString($object->name['en']);
		}		
	}
}