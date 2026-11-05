<?php


/**
 * PHP unit tests of the HaHeLa\SecurityTypeModel class 
 *  - Model eines Wertpapiertyps.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration security-type.xml
 */


declare(strict_types=1);


use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// traits
require_once '..'.DS.'modules'.DS.'trait'.DS.'accessor.php';
// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'security'.DS.'type.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'security'.DS.'type.php';


/**
 * SecurityTypeModel Test
 */
class SecurityTypeModelTest extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        

    // rest
    const SECURITY_TYPE_RESET_ID = 0;
    const SECURITY_TYPE_RESET_CODE = '';
    const SECURITY_TYPE_RESET_CFI_CODE = '';
    const SECURITY_TYPE_RESET_NAME_EN = '';
    const SECURITY_TYPE_RESET_NAME_DE_SINGULAR = '';
    const SECURITY_TYPE_RESET_NAME_DE_PLURAL = '';
    const SECURITY_TYPE_RESET_SORT = 0;
    
    // unknown => unbekannt / unbestimmt (z.Z nicht genutzt)
    const SECURITY_TYPE_INDEFINITE_ID = 1;
    const SECURITY_TYPE_INDEFINITE_CODE = 'unknown';
    const SECURITY_TYPE_INDEFINITE_CFI_CODE = null;
    const SECURITY_TYPE_INDEFINITE_NAME_DE_SINGULAR = 'unbekannt / unbestimmt';
    const SECURITY_TYPE_INDEFINITE_NAME_DE_PLURAL = 'unbekannt / unbestimmt';
    const SECURITY_TYPE_INDEFINITE_SORT = 1;
    
    // stock => Aktie
    const SECURITY_TYPE_STOCK_ID = 2;
    const SECURITY_TYPE_STOCK_CODE = 'stock';
    const SECURITY_TYPE_STOCK_CFI_CODE = 'ES';
    const SECURITY_TYPE_STOCK_NAME_EN = 'common/ordinary share';
    const SECURITY_TYPE_STOCK_NAME_DE_SINGULAR = 'Aktie';
    const SECURITY_TYPE_STOCK_NAME_DE_PLURAL = 'Aktien';
    const SECURITY_TYPE_STOCK_SORT = 2;

        
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
     * Erzeuge eine Instanz der Klasse HaHeLa\SecurityTypeModel.
     */
    public function testCreateSecurityTypeObject ()
    {
        // Zwei Pflichtparameter
        $model = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config);
        $this->assertTrue($model instanceof HaHeLa\SecurityTypeModel);
        $this->assertInstanceOf('HaHeLa\SecurityTypeModel', $model);
        
        // Zwei Pflichtparameter und ein optionaler Parameter.
        $options = array();
        $model = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config, $options);
        $this->assertTrue($model instanceof HaHeLa\SecurityTypeModel);
        $this->assertInstanceOf('HaHeLa\SecurityTypeModel', $model);
        
        // Zwei Pflichtparameter und zwei optionale Parameter.
        $options = array();
        $id = self::SECURITY_TYPE_STOCK_ID;
        $model = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config, $options, $id);
        $this->assertTrue($model instanceof HaHeLa\SecurityTypeModel);
        $this->assertInstanceOf('HaHeLa\SecurityTypeModel', $model);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_ID, $model->getId());
        $this->assertEquals(self::SECURITY_TYPE_STOCK_CODE, $model->getCode());
        $this->assertEquals(self::SECURITY_TYPE_STOCK_CFI_CODE, $model->getCfiCode());
        $this->assertEquals(self::SECURITY_TYPE_STOCK_NAME_EN, $model->getNameEn());
        $this->assertEquals(self::SECURITY_TYPE_STOCK_NAME_DE_SINGULAR, $model->getNameDeSingular());
        $this->assertEquals(self::SECURITY_TYPE_STOCK_NAME_DE_PLURAL, $model->getNameDePlural());
        $this->assertEquals(self::SECURITY_TYPE_STOCK_SORT, $model->getSort());
    }
    
    
    /**
     * Objekt der Klasse HaHeLa\SecurityTypeModel in ein Objekt der 
     *  Standardklasse \stdClass ueberfuehren.
     */
    public function testToStdObject ()
    {
        $id = self::SECURITY_TYPE_STOCK_ID;
        $model = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config, array(), $id);
        $this->assertTrue($model instanceof HaHeLa\SecurityTypeModel);
        $stdObject = $model->toStdObject();
        $this->assertInstanceOf(\stdClass::class, $stdObject);
        $this->assertEmpty($stdObject->options);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_ID, $stdObject->id);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_CODE, $stdObject->code);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_CFI_CODE, $stdObject->cfi_code);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_NAME_EN, $stdObject->name_en);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_NAME_DE_SINGULAR, $stdObject->name_de_singular);
        $this->assertEquals(self::SECURITY_TYPE_STOCK_NAME_DE_PLURAL, $stdObject->name_de_plural);        
        $this->assertEquals(self::SECURITY_TYPE_STOCK_SORT, $stdObject->sort);
    }
    
    
    /**
     * Objekt der Klasse HaHeLa\SecurityTypeModel auf Initialwerte zurueck 
     *  setzen.
     */
    public function testResetProperties ()
    {
        $id = self::SECURITY_TYPE_RESET_ID;
        $model = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config, array(), $id);
        $this->assertTrue($model instanceof HaHeLa\SecurityTypeModel);
        $model->resetProperties();
        $this->assertIsArray($model->getOptions());
        $this->assertEmpty($model->getOptions());
        $this->assertEquals(self::SECURITY_TYPE_RESET_ID, $model->getId());
        $this->assertEmpty($model->getCode());
        $this->assertEmpty($model->getCfiCode());
        $this->assertEmpty($model->getNameEn());
        $this->assertEmpty($model->getNameDeSingular());
        $this->assertEmpty($model->getNameDePlural());
        $this->assertEmpty($model->getSort());
    }
    
    
    /**
     * Existiert ein Wertpapiertyp in der Datenbank.
     */
    public function testExists ()
    {
       // Auf alle Werte testen.
       $options = array(
           'id' => self::SECURITY_TYPE_STOCK_ID,
           'code' => self::SECURITY_TYPE_STOCK_CODE,
           'cfi_code' => self::SECURITY_TYPE_STOCK_CFI_CODE,
           'name_en' => self::SECURITY_TYPE_STOCK_NAME_EN,
           'name_de_singular' => self::SECURITY_TYPE_STOCK_NAME_DE_SINGULAR,
           'name_de_plural' => self::SECURITY_TYPE_STOCK_NAME_DE_PLURAL,
           'sort' => self::SECURITY_TYPE_STOCK_SORT
       );
       $model = new \HaHeLa\SecurityTypeModel($this->dbh, $this->config);
       $this->assertTrue($model instanceof HaHeLa\SecurityTypeModel);
       $id = $model->setOptions($options)->exists();
       $this->assertEquals(self::SECURITY_TYPE_STOCK_ID, $id);
    }
}