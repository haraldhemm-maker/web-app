<?php


/**
 * PHP unit tests of the HaHeLa\DriverFinnhubModel class 
 *  - Model des Wertpapier-Treibers Finnhub.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration driver-finnhub.xml
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
require_once '..'.DS.'modules'.DS.'interface'.DS.'currency.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'security'.DS.'type.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'driver'.DS.'openfigi.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'driver'.DS.'general.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'country.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'currency.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'security'.DS.'type.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'driver'.DS.'openfigi.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'driver'.DS.'finnhub.php';


/**
 * DriverFinnhubModel Test
 */
class DriverFinnhubModelTest extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        
    
    const TEST_VERSION = '1.0.0';
    const TEST_NAME = 'Finnhub Stock API';
    const TEST_DESCRIPTION = 'Echtzeit-RESTful-APIs und WebSockets für Aktien, Währungen und Kryptowährungen. Machen Sie Finanzdaten für alle zugänglich – mit der leistungsstärksten Aktien-API auf dem Markt.';
    const TEST_DOMAIN = 'finnhub.io/';
    const TEST_URL = 'https://finnhub.io';
    
    const TEST_WAYNE_ENTERPRISES_STOCK_NAME = 'Wayne Enterprises';
    const TEST_WAYNE_ENTERPRISES_STOCK_ISIN = 'US94419W1043';
    // Fiktiver Wertpapiertyp ist COB (Climate Option Bond), "Klima-Optionsanleihe".
    const TEST_WAYNE_ENTERPRISES_STOCK_FINNHUB_TYPE = 'COB';
    
    const TEST_MICROSOFT_STOCK_NAME = 'Microsoft Corporation';
    const TEST_MICROSOFT_STOCK_ISIN = 'US5949181045';
    const TEST_MICROSOFT_STOCK_WKN  = '870747';
    const TEST_MICROSOFT_STOCK_TICKER_SYMBOL_US = 'MSFT';
    const TEST_MICROSOFT_STOCK_TICKER_SYMBOL_DE = 'MSF';
    const TEST_MICROSOFT_STOCK_COUNTRY = 'US';
    const TEST_MICROSOFT_STOCK_CURRENCY = 'USD';
    const TEST_MICROSOFT_STOCK_INDUSTRY = 'Technology';
    const TEST_MICROSOFT_STOCK_FINNHUB_TYPE = 'Common Stock';
    
    const TEST_UNKNOWN_TYPE_ID = 1;
    const TEST_UNKNOWN_TYPE_CODE = 'unknown';
    
    
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
    }
    
            
    /**
     * Erzeuge eine Instanz der Klasse HaHeLa\DriverFinnhubModel.
     */
    public function testCreateDriverFinnhubObject()
    {
        // Zwei Pflichtparameter
        $model = new \HaHeLa\DriverFinnhubModel($this->dbh, $this->config);
        $this->assertTrue($model instanceof HaHeLa\DriverFinnhubModel);
        $this->assertInstanceOf('HaHeLa\DriverFinnhubModel', $model);
        
        // Zwei Pflichtparameter und ein optionaler Parameter.
        $options = array();
        $model = new \HaHeLa\DriverFinnhubModel($this->dbh, $this->config, $options);
        $this->assertTrue($model instanceof HaHeLa\DriverFinnhubModel);
        $this->assertInstanceOf('HaHeLa\DriverFinnhubModel', $model);
        
        // Getter zu Treiber-Individualeigenschaften.
        $this->assertEquals(self::TEST_VERSION, $model->getVersion());
        $this->assertEquals(self::TEST_NAME, $model->getName());
        $this->assertEquals(self::TEST_DESCRIPTION, $model->getDescription());
        $this->assertEquals(self::TEST_DOMAIN, $model->getDomain());
        $this->assertEquals(self::TEST_URL, $model->getUrl());        
    }
    
    
    /**
     * Test der Aufloesung der ISIN.
     */
    public function testResolveIsin()
    {
        $model = new \HaHeLa\DriverFinnhubModel($this->dbh, $this->config);
        $this->assertInstanceOf('HaHeLa\DriverFinnhubModel', $model);
        // >>>>> Gueltige ISIN
        $result = $model->resolveIsin(self::TEST_MICROSOFT_STOCK_ISIN);
        $this->assertIsArray($result);
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_TICKER_SYMBOL_US, $result[0]);
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_NAME, $result[1]);
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_FINNHUB_TYPE, $result[2]);
        // Teste die entsprechenden Getter-Methoden
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_TICKER_SYMBOL_US, $model->getSecurityTicker());
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_NAME, $model->getSecurityName());
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_FINNHUB_TYPE, $model->getFinnhubType());
        // >>>>> Ungueltige ISIN
        $result = $model->resolveIsin(self::TEST_WAYNE_ENTERPRISES_STOCK_ISIN);
        $this->assertNull($result);
    }
    
    
    /**
     * Test mapping finnhub-Typ auf Systemtyp.
     */
    public function testMapFinnhubTypeToSystemType()
    {
        $model = new \HaHeLa\DriverFinnhubModel($this->dbh, $this->config);
        $this->assertInstanceOf('HaHeLa\DriverFinnhubModel', $model);
        
        // Gueltiger finnhub-Typ
        $systemTypeCode = $model->mapFinnhubTypeToSystemType(self::TEST_MICROSOFT_STOCK_FINNHUB_TYPE);
        $systemTypeId = $model->getSecurityTypeId();
        $this->assertGreaterThanOrEqual(1, $systemTypeId);
        $securityType = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config, $this->options, $systemTypeId);
        $this->assertEquals($systemTypeCode, $securityType->getCode());

        // Ungueltiger finnhub-Typ
        $systemTypeCode = $model->mapFinnhubTypeToSystemType(self::TEST_WAYNE_ENTERPRISES_STOCK_FINNHUB_TYPE);
        $systemTypeId = $model->getSecurityTypeId();
        $this->assertGreaterThanOrEqual(1, $systemTypeId);        
        $securityType = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config, $this->options, $systemTypeId);
        $this->assertEquals(self::TEST_UNKNOWN_TYPE_ID, $securityType->getId());
        $this->assertEquals(self::TEST_UNKNOWN_TYPE_CODE, $securityType->getCode());
    }
    
    
    /*
     * Test Wertpapierprofil anreichern.
     */
    public function testEnrichSecurityProfile()
    {
        $model = new \HaHeLa\DriverFinnhubModel($this->dbh, $this->config);
        $this->assertInstanceOf('HaHeLa\DriverFinnhubModel', $model);
        // Gueltiges Ticker-Symbol.
        $result = $model->enrichProfile(self::TEST_MICROSOFT_STOCK_TICKER_SYMBOL_US);
        $this->assertIsArray($result);
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_TICKER_SYMBOL_US, $model->getSecurityTicker());
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_NAME, $model->getSecurityName());
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_COUNTRY, $model->getSecurityCountry());
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_CURRENCY, $model->getSecurityCurrency());
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_INDUSTRY, $model->getSecurityIndustry());
    }
    
    
    /**
     * Test Wertpapierdaten holen mit Hilfe der ISIN.
     */
    public function testGetSecurityDataByIsin()
    {
        $model = new \HaHeLa\DriverFinnhubModel($this->dbh, $this->config);
        $this->assertInstanceOf('HaHeLa\DriverFinnhubModel', $model);
        // Gueltige ISIN
        $securityData = $model->getSecurityDataByIsin(self::TEST_MICROSOFT_STOCK_ISIN);
    }
}

