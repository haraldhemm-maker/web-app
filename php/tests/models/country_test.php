<?php

/**
 * PHP unit tests of the HaHeLa\CountryModel class 
 *  - Model eines Landes
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd ~harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration=country.xml
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
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'country.php';

/**
 * CountryModel_Test
 */
class CountryModel_Test extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        

    const DEUTSCHLAND_ID = 276;
    const DEUTSCHLAND_ALPHA2 = 'DE';            // 2-Buchstabencode in Grossbuchstaben.
    const DEUTSCHLAND_ALPHA3 = 'DEU';           // 3-Buchstabencode in Grossbuchstaben.
	const DEUTSCHLAND_NAME_DE = 'Deutschland';  // Landesname in deutscher Sprache.
	const DEUTSCHLAND_NAME_EN = 'Germany';  	// Landesname in englischer Sprache.
    
    /**
     * Datenbank(server)-Handle.
     * @var \PDO
     */
    public \PDO $dbh;

    /**
     * Konfiguration.
     * @var array
     */
    public array $config;

    /**
     * Ergebnis der Aktionen mit dem Datenbankserver eine Verbindung
     *  herzustellen und die Datenbank zu oeffnen.
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
    }

    
    /**
     * Erzeuge eine Instanz der Klasse HaHeLa\CountryModel.
     */
    public function testCreateCountryObject()
    {
        $model = new HaHeLa\CountryModel($this->dbh, $this->config, $this->options);
        $this->assertTrue($model instanceof HaHeLa\CountryModel);
        $this->assertInstanceOf('HaHeLa\CountryModel', $model);
    }
    
    
    /**
     * Erzeuge eine Instanz der Klasse HaHeLa\CountryModel mit optionalem 
     *  Parameter zur Identifikation eines Landesdatensatzes.
     */
    public function testCreateCountryObjectWithOptionalParameter()
    {
        $model = new HaHeLa\CountryModel($this->dbh, $this->config, $this->options, self::DEUTSCHLAND_ID);
        $this->assertInstanceOf('HaHeLa\CountryModel', $model);
        $country = new \stdClass();
        $country->id = $model->getId();
        $country->alpha_2 = $model->getAlpha2();
        $country->alpha_3 = $model->getAlpha3();
		$country->name_de = $model->getName(); // 'de' ist default
		$country->name_en = $model->getName('en');
        $stdObject = $model->toStdObject();
        foreach ($stdObject as $property => $value) {
            $this->assertEquals($country->$property, $value);
        }
    }
  
    
    /**
     * Ueberprueft ob ein Land (Staat) mit den gegebenen Eigenschaften 
     *  existiert.
     */
    public function testCountryExists() 
    {
        $model = new HaHeLa\CountryModel($this->dbh, $this->config);
        
        // Suche nur mit Hilfe der Identifikationsnummer.
        $this->options = array(
            'id' => self::DEUTSCHLAND_ID
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertEquals(self::DEUTSCHLAND_ID, $result);
        
        // Suche mit Hilfe der 2- UND der 3-Buchstabenkennung
        $this->options = array(
            'alpha_2' => self::DEUTSCHLAND_ALPHA2,
            'alpha_3' => self::DEUTSCHLAND_ALPHA3
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertEquals(self::DEUTSCHLAND_ID, $result);
        
        // Suche mit einer nicht existierenden 2-Buchstabenkennung.
        $this->options = array(
            'alpha_2' => 'XX'
        );
        $result = $model->setOptions($this->options)->exists();
        $this->assertFalse($result);

		// Bei Erzeugung des Modell auch die Optionen initialisieren.
		// 	Das erspart das nachtraegliche setzen der Optionen.
		$this->options = array(
			'alpha_2' => self::DEUTSCHLAND_ALPHA2
		);
		$model = new HaHeLa\CountryModel($this->dbh, $this->config);
		$result = $model->setOptions($this->options)->exists();
		$this->assertEquals(self::DEUTSCHLAND_ID, $result);
    }
    

    /**
     * Suche von Laendern (Staaten) die einen gleichen Namensbestandteil 
     *  haben.
     */
    public function testSearchCountryByName ()
    {
		// Suche in deutscher Sprache 'de' (Voreinstellung).
        $haystack = array('Belarus', 'Belgien', 'Belize');
        $model = new HaHeLa\CountryModel($this->dbh, $this->config);
        $nameFragment = 'Bel';
        $result = $model->searchByName($nameFragment);
        foreach ($result as $index => $row) {
            $model = new HaHeLa\CountryModel($this->dbh, $this->config, array(), $row['id']);
            $needle = $model->getName();
            $this->assertTrue(in_array($needle, $haystack));
        }
		// Suche in englischer Sprache 'en'.
		$haystack = array('Belarus', 'Belgium', 'Belize');
		$model = new HaHeLa\CountryModel($this->dbh, $this->config);
		$nameFragment = 'Bel';
		$result = $model->searchByName($nameFragment, 'en');
		foreach ($result as $index => $row) {
		    $model = new HaHeLa\CountryModel($this->dbh, $this->config, array(), $row['id']);
		    $needle = $model->getName('en');
		    $this->assertTrue(in_array($needle, $haystack));
		}
    }
    
    
    /**
     * Ermittelt den 2-Buchstabencode
     */
    public function testDetermine2LetterCode ()
    {
        $model = new HaHeLa\CountryModel($this->dbh, $this->config);

        // 2-Buchstabencode bestimmt aus dem 2-Buchstabencode.
        $alpha2 = $model->determine2LetterCode(self::DEUTSCHLAND_ALPHA2);
        $this->assertEquals(self::DEUTSCHLAND_ALPHA2, $alpha2);
        // 2-Buchstabencode bestimmt aus dem 3-Buchstabencode.
        $alpha2 = $model->determine2LetterCode(self::DEUTSCHLAND_ALPHA3);
        $this->assertEquals(self::DEUTSCHLAND_ALPHA2, $alpha2);
        
        // 2-Buchstabencode bestimmt aus dem Landesnamenfragment.
        $alpha2 = $model->determine2LetterCode(self::DEUTSCHLAND_NAME_DE);
        $this->assertEquals(self::DEUTSCHLAND_ALPHA2, $alpha2);
    }
    
    
    /**
     * Die wichtigsten Eigenschaften eines Objektes der Klasse
     *  \HaHeLa\CountryModel in ein Objekt der Standardklasse \stdClass 
     *  uebertragen.
     */
    public function testToStdObject ()
    {
        $model = new HaHeLa\CountryModel($this->dbh, $this->config, $this->options, self::DEUTSCHLAND_ID);
        $stdObject = $model->toStdObject();
        $this->assertInstanceOf('\stdClass', $stdObject);
		$this->assertEquals(self::DEUTSCHLAND_ID, $stdObject->id);
		$this->assertEquals(self::DEUTSCHLAND_ALPHA2, $stdObject->alpha_2);
		$this->assertEquals(self::DEUTSCHLAND_ALPHA3, $stdObject->alpha_3);
		$this->assertEquals(self::DEUTSCHLAND_NAME_DE, $stdObject->name_de);
		$this->assertEquals(self::DEUTSCHLAND_NAME_EN, $stdObject->name_en);
    }
}