<?php


/**
 * PHP unit tests of the HaHeLa\SecurityTypesModel class 
 *  - Model aller Wertpapiertypen.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration=security-types.xml
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
require_once '..'.DS.'modules'.DS.'interface'.DS.'security'.DS.'types.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'security'.DS.'type.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'security'.DS.'types.php';


/**
 * SecurityTypesModel Test
 */
class SecurityTypesModelTest extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        

    
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
     * Erzeuge eine Instanz der Klasse HaHeLa\Modules_Model_Security_Types.
     */
    public function testCreateSecurityTypesObject ()
    {
        $model = new \HaHeLa\SecurityTypesModel($this->dbh, $this->config);
        $this->assertTrue($model instanceof HaHeLa\SecurityTypesModel);
        $this->assertInstanceOf('HaHeLa\SecurityTypesModel', $model);
    }
    
    
    /**
     * List mit allen Wertpapiertypen als Objekte der Standardklasse.
     */
    public function testGetStdObjList () 
    {
        $model = new \HaHeLa\SecurityTypesModel($this->dbh, $this->config);
        $list = $model->getStdObjList();
        $this->assertIsArray($list);
        foreach ($list as $stdObject) {
            $this->assertInstanceOf('\stdClass', $stdObject);
        }
    }
}