<?php


/**
 * PHP unit tests of the HaHeLa\UserModel class 
 *  - Model des Benutzers.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration user.xml
 */


declare(strict_types=1);
use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'configuration.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'user.php';


// models
require_once '..'.DS.'modules'.DS.'model'.DS.'configuration.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'user.php';


/**
 * Configuration Model Test
 */
class UserModelTest extends TestCase
{
    const HARALD_ID = 1;
    const HARALD_SUB = 1;
    const HARALD_FIRST_NAME = 'Harald';
    const HARALD_LAST_NAME = 'Hemm';
    
    const JUNK_SUB = 9999;
    const JUNK_FIRST_NAME = 'First Junk';
    const JUNK_LAST_NAME = 'Last Junk';
    const JUNK_LAST_NAME_UPDATE = 'Junk Last';
    
    
    public $dbhResult;
    public $dbh;
    public $config;
    public $options = array();
    
    
    /**
     * Setup of the PHP unit test.
     *
     * @return void
     */
    protected function setUp (): void
    {
        //
        // Konfiguration laden.
        //
        // Fully Qualified File Name of the configuration file.
        // (vollqualifizierter Dateiname: 
        //  Pfad + Dateihauptname + Dateinameerweiterung
        // )
        $fqfn = '..' . DS . '..' . DS . 'dat' . DS . 'configuration-' 
            . strtolower(gethostname())
            .'-'.strtolower(php_uname('s'))
            .'.ini';
        $this->config = parse_ini_file($fqfn, true);
        
        //      
        // Verbindung mit Datenbankserver herstellen und Datenbank oeffnen.
        //
        $this->dbhResult = new stdClass();
        $dataSourceName = "mysql:host={$this->config['DATABASE']['SERVERNAME']};dbname={$this->config['DATABASE']['DATABASENAME']}";
        try {
          $this->dbh = new PDO($dataSourceName, $this->config['DATABASE']['USERNAME'], $this->config['DATABASE']['PASSWORD']);
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
     * Erzeuge eine Instanz der Klasse UserModel.
     *  Ohne optionale Optionen.
     */
    public function testCreateUserObject()
    {
        $model = new HaHeLa\UserModel($this->dbh, $this->config);
        $this->assertTrue($model instanceof HaHeLa\UserModel);
        $this->assertInstanceOf('HaHeLa\UserModel', $model);
    }
    
    
    /**
     * Erzeuge eine Instanz der Klasse UserModel.
     *  Mit optionale Optionen.
     */
    public function testCreateUserObjectWithOptions()
    {
        $model = new HaHeLa\UserModel($this->dbh, $this->config, $this->options);
        $this->assertTrue($model instanceof HaHeLa\UserModel);
        $this->assertInstanceOf('HaHeLa\UserModel', $model);
    }
    
    
    /**
     * Erzeuge eine Instanz der Klasse UserModel.
     *  Mit optionale Optionen und optionaler Identifikationsnummer.
     */
    public function testCreateUserObjectWithOptionsAndId()
    {
        $model = new HaHeLa\UserModel($this->dbh, $this->config, $this->options, self::HARALD_ID);
        $this->assertTrue($model instanceof HaHeLa\UserModel);
        $this->assertInstanceOf('HaHeLa\UserModel', $model);
        $data = $model->toArray();
        $this->assertEquals(self::HARALD_ID, $data['id']);
        $this->assertEquals(self::HARALD_SUB, $data['sub']);
        $this->assertEquals(self::HARALD_FIRST_NAME, $data['first_name']);
        $this->assertEquals(self::HARALD_LAST_NAME, $data['last_name']);
        $this->assertNotEmpty($data['created']);
        $this->assertNotEmpty($data['modified']);
    }
    
    
    /**
     * Create, Read, Update and Delete eine Benutzerkonto-Datensatz in der 
     *  lokalen Datenbank.
     */
    public function testCreateReadUpdateDeleteUserModel()
    {
        // CREATE
        $model = new HaHeLa\UserModel($this->dbh, $this->config);
        $junkId = $model->setSub(self::JUNK_SUB)
        ->setFirstName(self::JUNK_FIRST_NAME)
        ->setLastName(self::JUNK_LAST_NAME)
        ->create();
        $this->assertGreaterThan(0, $junkId);
        unset($model);
        // UPDATE
        $model = new HaHeLa\UserModel($this->dbh, $this->config);
        $result = $model->setId($junkId)
        ->setSub(self::JUNK_SUB)
        ->setFirstName(self::JUNK_FIRST_NAME)
        ->setLastName(self::JUNK_LAST_NAME_UPDATE)
        ->update();
        $this->assertTrue($result);
        unset($model);
        // READ
        $model = new HaHeLa\UserModel($this->dbh, $this->config);
        $result = $model->setId($junkId)
        ->read();
        $this->assertInstanceOf('HaHeLa\UserModel', $result);
        $data = $result->toArray();        
        $this->assertEquals($junkId, $data['id']);
        $this->assertEquals(self::JUNK_SUB, $data['sub']);
        $this->assertEquals(self::JUNK_FIRST_NAME, $data['first_name']);
        $this->assertEquals(self::JUNK_LAST_NAME_UPDATE, $data['last_name']);
        $this->assertNotEmpty($data['created']);
        $this->assertNotEmpty($data['modified']);
        unset($model);
        unset($result);
        // DELETE
        $model = new HaHeLa\UserModel($this->dbh, $this->config, $this->options, $junkId);
        $result = $model->delete();
        $this->assertTrue($result);        
    }
}