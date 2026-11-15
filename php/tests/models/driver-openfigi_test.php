<?php


/**
 * PHP unit tests of the HaHeLa\DriverOpenfigiModel class - Test des Modells 
 *  zur Bestimmung der Globalen Identifikatoren fuer Finanzinstrumente 
 *  (Open Financial Instrument Global Identifier OpenFIFI) (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd ~/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration driver-openfigi.xml
 */


declare(strict_types=1);


use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// traits
require_once '..'.DS.'modules'.DS.'trait'.DS.'accessor.php';
// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'driver'.DS.'openfigi.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'driver'.DS.'openfigi.php';


/**
 * DriverOpenfigiModel Test
 */
class DriverOpenfigiModel_Test extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        
    
    const TEST_WAYNE_ENTERPRISES_STOCK_NAME = 'Wayne Enterprises';
    const TEST_WAYNE_ENTERPRISES_STOCK_ISIN = 'US94419W1043';
    // Fiktiver Wertpapiertyp ist COB (Climate Option Bond), "Klima-Optionsanleihe".
    const TEST_WAYNE_ENTERPRISES_STOCK_FINNHUB_TYPE = 'COB';
    
    const TEST_MICROSOFT_STOCK_NAME = 'Microsoft Corporation';
    const TEST_MICROSOFT_STOCK_ISIN = 'US5949181045';
    const TEST_MICROSOFT_STOCK_WKN  = '870747';
    const TEST_MICROSOFT_STOCK_TICKER_SYMBOL_US = 'MSFT';
    const TEST_MICROSOFT_STOCK_COUNTRY = 'US';
    const TEST_MICROSOFT_STOCK_CURRENCY = 'USD';
    const TEST_MICROSOFT_STOCK_INDUSTRY = 'Technology';
    const TEST_MICROSOFT_STOCK_FINNHUB_TYPE = 'Common Stock';
    
    const TEST_MICROSOFT_STOCK_FIGI = 'BBG000DMWN19';
    const TEST_MICROSOFT_STOCK_TICKER_SYMBOL_DE = 'MSF';
    const TEST_MICROSOFT_STOCK_EXCHCODE = 'GY';
    const TEST_MICROSOFT_STOCK_SECURITY_TYPE = 'Common Stock';
    const TEST_MICROSOFT_STOCK_SECURITY_TYPE2 = 'Common Stock';
    const TEST_MICROSOFT_STOCK_MARKET_SECTOR = 'Equity';
    const TEST_MICROSOFT_STOCK_SHORT_NAME = 'MICROSOFT CORP';
    const TEST_MICROSOFT_STOCK_COMPOSITE_FIGI = 'BBG000DMWLT3';
    const TEST_MICROSOFT_STOCK_MIC_XETRA = 'XETR'; // MIC der Deutsche Boerse Xetra. 
    
    const TEST_UNKNOWN_TYPE_ID = 1;
    const TEST_UNKNOWN_TYPE_CODE = 'unknown';
    
    // XETR ist das amtliche Boersenkuerzel (MIC-Code) fuer Xetra, das 
    //  vollelektronische Handelssystem und den wichtigsten Referenzmarkt der 
    //  Deutsche Boerse AG mit Sitz in Frankfurt am Main. Xetra steht fuer 
    //  Exchange Electronic Trading (elektronischer Boersenhandel).
    const TEST_MIC_EXCHANGE_ELECTRONIC_TRADING = 'XETR';
    
    // NEW YORK STOCK EXCHANGE, INC.
    const TEST_MIC_NEW_YORK_STOCK_EXCHANGE = 'XNYS';
    
    /**
     * Eigenschaften nach der Ermittlung der Wertpapierdaten mit der Angabe 
     *  von ISIN und MIC.
     * @var array
     */
    public array $protectedProperties = array(
        'options' => array(),
        'figi' => self::TEST_MICROSOFT_STOCK_FIGI,
        'ticker' => self::TEST_MICROSOFT_STOCK_TICKER_SYMBOL_DE,
        'exch_code' => self::TEST_MICROSOFT_STOCK_EXCHCODE,
        'security_type' => self::TEST_MICROSOFT_STOCK_SECURITY_TYPE,
        'security_type_2' => self::TEST_MICROSOFT_STOCK_SECURITY_TYPE2,
        'market_sector' => self::TEST_MICROSOFT_STOCK_MARKET_SECTOR,
        'name' => self::TEST_MICROSOFT_STOCK_SHORT_NAME,
        'composite_figi' => self::TEST_MICROSOFT_STOCK_COMPOSITE_FIGI,
        'mic' => self::TEST_MICROSOFT_STOCK_MIC_XETRA
    );
    
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
     * Erzeuge eine Instanz der Klasse HaHeLa\DriverOpenfigiModel.
     */
    public function testCreateDriverOpenfigiObject()
    {
        // Zwei Pflichtparameter
        $model = new \HaHeLa\DriverOpenfigiModel($this->dbh, $this->config);
        $this->assertTrue($model instanceof HaHeLa\DriverOpenfigiModel);
        $this->assertInstanceOf('HaHeLa\DriverOpenfigiModel', $model);
        
        // Zwei Pflichtparameter und ein optionaler Parameter.
        $options = array();
        $model = new \HaHeLa\DriverOpenfigiModel($this->dbh, $this->config, $options);
        $this->assertTrue($model instanceof HaHeLa\DriverOpenfigiModel);
        $this->assertInstanceOf('HaHeLa\DriverOpenfigiModel', $model);
    }
    
    
    /**
     * Finde mit der ISIN die globalen Identifikatoren fuer Finanzinstrumente.
     */
    public function testFigiByIsin()
    {
        $model = new \HaHeLa\DriverOpenfigiModel($this->dbh, $this->config);
        $this->assertInstanceOf('HaHeLa\DriverOpenfigiModel', $model);
        
        // Mit Pflichtparameter ISIN und ohne optionale Parameter.
        $result = $model->figiByIsin(self::TEST_MICROSOFT_STOCK_ISIN);
        $this->assertIsArray($result);
        $this->assertGreaterThan(1, count($result));
        
        // Mit Pflichtparameter ISIN und optionalem Parameter $code:
        //  $code = 'micCode=XETR'
        $code = 'micCode='.self::TEST_MICROSOFT_STOCK_MIC_XETRA;
        $result = $model->figiByIsin(self::TEST_MICROSOFT_STOCK_ISIN, $code);
        $this->assertEquals(1, count($result));
        
        // Mit Pflichtparameter ISIN und optionalem Parameter $code:
        //  $code = 'exchCode=GY'
        $code = 'exchCode='.self::TEST_MICROSOFT_STOCK_EXCHCODE;
        $result = $model->figiByIsin(self::TEST_MICROSOFT_STOCK_ISIN, $code);
        $this->assertEquals(1, count($result));
    }
    
    
    /**
     * Getter-Methoden fuer protected Objekt-Eigenschaften.
     */
    public function testGetterMethods()
    {
        $model = new \HaHeLa\DriverOpenfigiModel($this->dbh, $this->config);
        $this->assertInstanceOf('HaHeLa\DriverOpenfigiModel', $model);
        
        // proteced Eigenschaften zuruecksetzen.
        $model->resetProperties();

        // Mit Pflichtparameter ISIN und optionalem Parameter $code
        $code = 'exchCode='.self::TEST_MICROSOFT_STOCK_EXCHCODE;
        $result = $model->figiByIsin(self::TEST_MICROSOFT_STOCK_ISIN, $code);
        $this->assertEquals(1, count($result));
        foreach ($result[0] as $key => $valueFromResult) {
            //
            // Getter Methoden
            //
            $methodName = 'get' . ucfirst($key);
            $valueFromMethod = $model->$methodName();
            $this->assertEquals($valueFromResult, $valueFromMethod);
            // Vergleich mit konstanten Vorgaben.
            $snakeKey = $model->camelCaseToSnake($key);
            $this->assertEquals($this->protectedProperties[$snakeKey], $valueFromResult);
        }
        //
        // Die MIC der Frankfurter Boerse sollte vorhanden sein.
        //
        $mic = $model->getMic();
        $this->assertEquals(self::TEST_MICROSOFT_STOCK_MIC_XETRA, $mic);
    }
}

