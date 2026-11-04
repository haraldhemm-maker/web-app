<?php


/**
 * PHP unit tests of the HaHeLa\CurrencyModel class 
 *  - Model einer Waehrung
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration=currency.xml
 */


declare(strict_types=1);
use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// traits
require_once '..'.DS.'modules'.DS.'trait'.DS.'accessor.php';
// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'currency.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'currency.php';


/**
 * CurrencyModel_Test
 */
class CurrencyModel_Test extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        
    
    const EURO_ID = 43;
    const EURO_CURRENCY = 'EUR';    // Waehrugsabkuerzung (in Grossbuchstaben).
    const EURO_NAME_EN = 'Euro';    // Waehrungsname in Englisch.
    const EURO_SYMBOL = '€';        // Waehrungssymbol.
    
    // Waehrungen mit Namensbestandteil 'Franc'.
    const FRANC_CURRENCY_IDS = array(
        16  => 'BIF',
        26  => 'CDF',
        27  => 'CHF',
        35  => 'DJF',
        51  => 'GNF',
        72  => 'KMF',
        116 => 'RWF',
        149 => 'XAF',
        158 => 'XFU',
        159 => 'XOF',
        161 => 'XPF'
    );
    
    
    /**
     * Konfiguration.
     * @var array
     * @default null
     */
    public $config = null;

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
     * Erzeuge eine Instanz der Klasse HaHeLa\CurrencyModel 
     *  ohne optionalen Parameter der Identifikationsnummer.
     */
    public function testCreateCurrencyObject ()
    {
        $model = new HaHeLa\CurrencyModel($this->dbh, $this->config, $this->options);
        $this->assertTrue($model instanceof HaHeLa\CurrencyModel);
        $this->assertInstanceOf('HaHeLa\CurrencyModel', $model);
    }
    
    
    /**
     * Erzeuge eine Instanz der Klasse HaHeLa\Modules_Model_Currency 
     *  mit optionalem Parameter zur Identifikation eines 
     *  Waehrungsdatensatzes.
     */
    public function testCreateCurrencyObjectWithOptionalParameter ()
    {
        $model = new HaHeLa\CurrencyModel($this->dbh, $this->config, $this->options, self::EURO_ID);
        $this->assertInstanceOf('HaHeLa\CurrencyModel', $model);
        $euro = new \stdClass();
        $euro->id = $model->getId();
        $euro->currency = $model->getCurrency();
        $euro->name_en = $model->getNameEn();
        $euro->symbol = $model->getSymbol();
        $currency = $model->toStdObject();
        foreach ($currency as $property => $value) {
            $this->assertEquals($euro->$property, $value);
        }
    }
    
    
    /**
     * Ueberprueft ob eine Waehrung mit den gegebenen Eigenschaften 
     *   in der Datenbank existiert.
     */
    public function testCurrencyExists () 
    {
        $model = new HaHeLa\CurrencyModel($this->dbh, $this->config);
        
        // Suche nur mit Hilfe der Identifikationsnummer.
        $this->options = array(
            'id' => self::EURO_ID
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertEquals(self::EURO_ID, $result);
        
        // Suche mit Hilfe der Waehrungsabkuerzung.
        $this->options = array(
            'currency' => self::EURO_CURRENCY
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertEquals(self::EURO_ID, $result);
        
        // Suche mit einer nicht existierenden Waehrungsabkuerzung.
        $this->options = array(
            'currency' => 'XXX'
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertFalse($result);
        
        // Suche mit Hilfe des englischen Namens und des Waehrungssymbols.
        $this->options = array(
            'name_en' => self::EURO_NAME_EN,
            'symbol' => self::EURO_SYMBOL
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertEquals(self::EURO_ID, $result);        
    }
    
    
    /**
     * Suche von Waehrungen, die einen gleichen Namensbestandteil 
     *   haben.
     */
    public function testSearchCurrencyByName ()
    {
        $model = new HaHeLa\CurrencyModel($this->dbh, $this->config);
        $nameFragment = 'Franc';
        $result = $model->searchByName($nameFragment);
        $haystack = array_keys(self::FRANC_CURRENCY_IDS);
        foreach ($result as $row) {
            $needle = intval($row['id']);
            $this->assertTrue(in_array($needle, $haystack));
        }
    }
    
    
    /**
     * Die oeffentlichen Eigenschaften eines Objektes der Klasse
     * \HaHeLa\Modules_Model_Currency in ein Objekt der Standardklasse
     * \stdClass uebertragen.
     */
    public function testToStdObject ()
    {
        $model = new HaHeLa\CurrencyModel($this->dbh, $this->config, $this->options, self::EURO_ID);
        $stdObject = $model->toStdObject();
        $this->assertInstanceOf('\stdClass', $stdObject);
    }
    
    
    /**
     * Setzt protected Eigenschaften des Waehrungsobjekts auf vordefiierte 
     *  Werte.
     */
    public function testRestProperties ()
    {
        $model = new HaHeLa\CurrencyModel($this->dbh, $this->config, $this->options, self::EURO_ID);
        $result = $model->resetProperties();
        $this->assertInstanceOf('HaHeLa\CurrencyModel', $result);        
        
    }
}