<?php


/**
 * PHP unit tests of the HaHeLa\CountriesModel class 
 *  - Model der Laender (Staaten)
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration=countries.xml
 */


declare(strict_types=1);


use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// traits
require_once '..'.DS.'modules'.DS.'trait'.DS.'accessor.php';
// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'country.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'countries.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'country.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'countries.php';


/**
 * CountriesModel_Test
 */
class CountriesModel_Test extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        
    
    const DEUTSCHLAND_ID = 276;
    const DEUTSCHLAND_ALPHA2 = 'de';            // 2-Buchstabencode in Kleinbuchstaben.
    const DEUTSCHLAND_ALPHA3 = 'deu';           // 3-Buchstabencode in Kleinbuchstaben.
	const DEUTSCHLAND_NAME_DE = 'Deutschland';  // Landesname in deutscher Sprache.
	const DEUTSCHLAND_NAME_EN = 'Germany';  	// Landesname in englischer Sprache.
    
        
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
    public \stdClass $dbhResult;

    /**
     * Optionen - zur Zeit unbenutzt.
     * @var array
     */
    public array $options = array();
    
    /**
     * Standardobjekt mit Daten von Deutschland.
     * @var 
     */
    public $stdObjectDE;

    
    /**
     * Setup of the PHP unit test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Konfiguration laden.
        $osName = php_uname('s');
        $fileName = str_replace('*', strtolower($osName), self::CONFIGURATION_BASEFILE_NAME_TEMPLATE);
        $fileName = realpath(__DIR__.DS.self::CONFIGURATION_PATH) . DS . $fileName;
        $this->config = parse_ini_file($fileName, true);

        // Verbindung mit Datenbankserver herstellen und Datenbank oeffnen.
        $this->dbhResult = new stdClass();
        $dataSourceName = "mysql:host={$this->config['DATABASE']['SERVERNAME']};dbname={$this->config['DATABASE']['DATABASENAME']}";
        try {
          $this->dbh = new \PDO($dataSourceName, $this->config['DATABASE']['USERNAME'], $this->config['DATABASE']['PASSWORD']);
          // set the PDO error mode to exception
          $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
          $this->dbhResult->result = true;
          $this->dbhResult->code = 0;
          $this->dbhResult->message = 'Eine Verbindung zum Datenbankserver und zur Datenbank konnte hergestellt werden. ';
        }
        catch(\PDOException $e) {
          $this->dbhResult->result = false;
          $this->dbhResult->code = $e->getCode();
          $this->dbhResult->message = 'Eine Verbindung zum Datenbankserver bzw. zur Datenbank konnte NICHT hergestellt werden: ' . $e->getMessage();
        }
        if (false === $this->dbhResult->result) {
          die($this->dbhResult->message . $this->dbhResult->code);
        }
        
        // Initialisierung 
        $this->stdObjectDE = new \stdClass();
        $this->stdObjectDE->id = self::DEUTSCHLAND_ID;
        $this->stdObjectDE->alpha_2 = self::DEUTSCHLAND_ALPHA2;
        $this->stdObjectDE->alpha_3 = self::DEUTSCHLAND_ALPHA3;
        $this->stdObjectDE->name_de = self::DEUTSCHLAND_NAME_DE;
    }

    
    /**
     * Erzeuge eine Instanz der Klasse HaHeLa\CountriesModel.
     */
    public function testCreateCountriesObject()
    {
        $model = new HaHeLa\CountriesModel($this->dbh, $this->config, $this->options);
        $this->assertTrue($model instanceof HaHeLa\CountriesModel);
        $this->assertInstanceOf('HaHeLa\CountriesModel', $model);
    }
    
    
    /**
     * Holt eine Liste aller Identifikationsnummern der Landesdatensaetze.
     */
    public function testGetIdList() 
    {
        $model = new HaHeLa\CountriesModel($this->dbh, $this->config, $this->options);
        $ids = $model->getIdList();
        $this->assertIsArray($ids);
        $this->assertContains(self::DEUTSCHLAND_ID, $ids);
    }
    
    
    /**
     * Holt eine Liste mit Standardobjekten bestehend aus Daten der 
     *  Laenderdatensaetze.
     */
    public function testGetListOfStdObjects()
    {
        $model = new HaHeLa\CountriesModel($this->dbh, $this->config, $this->options);
        $stdObjList = $model->getListOfStdObjects();
        $this->assertIsArray($stdObjList);
    }
}