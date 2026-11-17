<?php
/**
 * PHP unit tests of the HaHeLa\LeiModel class 
 *  - Model des Legal-Entity-Identifier (LEI).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.1
 * V1.0.1: CRUD-Tests hinzugefuegt.
 * @since 1.0.0
 *
 * Run test:
 * > cd ~harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration=lei.xml
 * 
 */
declare(strict_types=1);
use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'sqldb.php';
require_once '..'.DS.'modules'.DS.'interface'.DS.'lei.php';
// models
require_once '..'.DS.'modules'.DS.'model'.DS.'sqldb.php';
require_once '..'.DS.'modules'.DS.'model'.DS.'lei.php';


/**
 * LeiModel_Test
 */
class LeiModel_Test extends TestCase
{
    /**
     * Konstanten
     */
    const CONFIGURATION_PATH = '..'.DS.'..'.DS.'..'.DS.'dat'; // OHNE terminierendem Backslash bzw. Slash
    const CONFIGURATION_BASEFILE_NAME_TEMPLATE = 'configuration-rappelkiste-*.ini';        

	/**
	 * Pseudo Konstanten
	 */
	public $TEST = array(
		'id' => 65447,
		'lei' => '391200OUOEWDQSEJ0Y74',
		'legal_name' => 'Frankfurter Wertpapierbörse',
		'legal_name_lang' => 'de',
		'legal_addr_line1' => 'Börsenplatz 4',
		'legal_addr_line2' => '',
		'legal_addr_city' => 'Frankfurt am Main',
		'legal_addr_region' => 'DE-HE',
		'legal_addr_postal_code' => '60313',
		'legal_addr_country' => 'DE',
		'hq_addr_line1' => 'Börsenplatz 4',
		'hq_addr_city' => 'Frankfurt am Main',
		'hq_addr_region' => 'DE-HE',
		'hq_addr_postal_code' => '60313',
		'hq_addr_country' => 'DE',
		'legal_jurisdiction' => 'DE',
		'legal_form_code' => 'XLWA',
		'legal_form_other' => '',
		'entity_status' => 'ACTIVE',
		'entity_expiration_date' => '',
		'entity_expiration_reason' => '',
		'initial_registration_date' => '2016-04-20 14:24:01',
		'last_update_date' => '2025-03-06 08:47:03',
		'registration_status' => 'ISSUED',
		'next_renewal_date' => '2026-04-20 14:24:01',
		'managing_lou' => '39120001KULK7200U106',
		'corroboration_level' => '',
		'validated_at_id' => '',
		'validated_as' => '',
		'created_at' => '2026-04-12 21:20:52',
		'updated_at' => '2026-04-12 21:20:52' 
	);
	
	public $FAKE = array(
		'id' 						=> '0',
		'lei' 						=> '529900XXXXFAKE000119',
		'legal_name' 				=> 'Wayne Enterprises',
		'legal_name_lang' 			=> 'en',
		'legal_addr_line1' 			=> 'Wayne Enterprises Inc.',
		'legal_addr_line2' 			=> '1007 Mountain Drive',
		'legal_addr_city' 			=> 'Gotham City',
		'legal_addr_region' 		=> 'US-NJ',
		'legal_addr_postal_code' 	=> '07001',
		'legal_addr_country' 		=> 'US',
		'hq_addr_line1' 			=> '1 Wayne Tower',
		'hq_addr_city' 				=> 'Gotham City',
		'hq_addr_region' 			=> 'US-NJ',
		'hq_addr_postal_code' 		=> '07002',
		'hq_addr_country' 			=> 'US',
		'legal_jurisdiction' 		=> 'US-NJ',
		'legal_form_code' 			=> 'XTIQ',
		'legal_form_other' 			=> '',
		'entity_status' 			=> 'ACTIVE',		
		'entity_expiration_date' 	=> '',
		'entity_expiration_reason' 	=> '',
		'initial_registration_date' => '2015-03-12 09:30:00',
		'last_update_date' 			=> '2026-06-01 14:45:00',
		'registration_status' 		=> 'ISSUED',
		'next_renewal_date' 		=> '2027-03-12 09:30:00',
		'managing_lou' 				=> '5493001KJTIIGC8Y1R12',
		'corroboration_level' 		=> '',
		'validated_at_id' 			=> 'RA000665',
		'validated_as' 				=> 'FAKE-0001-GOTHAM',
		'created_at' 				=> '1989-10-26 00:00:00',
		'updated_at' 				=> ''
	);
    
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
	 * Erzeuge eine Instanz der Klasse \HaHeLa\LeiModel.
	 */
	public function testCreateLeiObject()
	{
	    $model = new \HaHeLa\LeiModel($this->dbh, $this->config, $this->options);
	    $this->assertTrue($model instanceof \HaHeLa\LeiModel);
	    $this->assertInstanceOf('\HaHeLa\LeiModel', $model);
	}


	/**
	 * Erzeuge eine Instanz der Klasse HaHeLa\Modules_Model_Lei mit 
	 * 	optionalem Parameter.
	 */
	public function testCreateLeiObjectWithOptionalParam ()
	{
	    $model = new \HaHeLa\LeiModel($this->dbh, $this->config, $this->options, $this->TEST['id']);
	    $this->assertTrue($model instanceof \HaHeLa\LeiModel);
	    $this->assertInstanceOf('\HaHeLa\LeiModel', $model);
        $this->assertEquals($this->TEST['id'], $model->getId());
        /*
		$properties = $model->getProperties();
		foreach ($properties as $propName => $propValue) {
			$propValue = $model->getProperty($propName);
			$constValue = $this->TEST[$propName];
			$this->assertEquals($constValue, $propValue);
		}
        */
	}
    
    	
	/**
	 * Erzeuge eine Instanz der Klasse HaHeLa\Modules_Model_Lei mit 
	 * 	optionalem aber ungueltigen Parameter.
	 */
/*
	public function testCreateLeiObjectWithInvalidOptionalParam ()
	{
	    $model = new HaHeLa\Modules_Model_Lei($this->dbh, $this->config, $this->options, 0);
	    $this->assertTrue($model instanceof HaHeLa\Modules_Model_Lei);
	    $this->assertInstanceOf('HaHeLa\Modules_Model_Lei', $model);
		$this->assertEquals(0, $model->getId());
		$this->assertIsInt($model->getId());
		$this->assertEmpty($model->getOptions());
		$this->assertIsArray($model->getOptions());
		$this->assertEmpty($model->getProperties());
		$this->assertIsArray($model->getProperties());
	}
*/
	
	/**
	 * Existieren LEI-Datensaetze mit den gegebenen Eigenschaften.
	 */
/*
	public function testExistsLeis ()
	{
		$this->options = array(
			'legal_name' => 'Frankfurter Wertpapierbörse',
			'legal_name_lang' => 'de',
			'legal_addr_line1' => 'Börsenplatz 4',
			'legal_addr_line2' => '',
			'legal_addr_city' => 'Frankfurt am Main',
			'legal_addr_region' => 'DE-HE',
			'legal_addr_postal_code' => '60313',
			'legal_addr_country' => 'DE'
		);
		$model = new HaHeLa\Modules_Model_Lei($this->dbh, $this->config, $this->options);
		$idList = $model->exists();
		$this->assertIsArray($idList);
		$this->assertEquals($this->TEST['id'], $idList[0]);
	}
*/
	
	/**
	 * Daten eines LEI-Objekts in ein Objekt der Standardklasse 
	 * 	umwandeln.
	 */
/*
	public function testToStandardObject ()
	{
		$model = new HaHeLa\Modules_Model_Lei($this->dbh, $this->config, $this->options, $this->TEST['id']);
		$stdObject = $model->toStdObject();
		$this->assertIsInt($stdObject->id);
		$this->assertEquals($this->TEST['id'], $stdObject->id);
		$this->assertIsArray($stdObject->options);
		$this->assertEmpty($stdObject->options);
		$this->assertIsArray($stdObject->properties);
		foreach ($stdObject->properties as $propName => $propValue) {
			$this->assertEquals($this->TEST[$propName], $propValue);
		}
	}
*/
	
	/**
	 * Suche LEI in der Datenbank.
	 */
/*
	public function testSearchLei ()
	{
		$model = new HaHeLa\Modules_Model_Lei($this->dbh, $this->config, $this->options);
		$result = $model->searchLei($this->TEST['lei']);
		$this->assertTrue($result);	
		$this->assertEquals($this->TEST['id'], $model->getId());	
	}
*/
	
	/**
	 * Pruefe die Syntax des Legal Entity Identifiers.
	 */
/*
	public function testValidateLeiSyntax ()
	{
		$model = new HaHeLa\Modules_Model_Lei($this->dbh, $this->config, $this->options, $this->TEST['id']);
		// Valide LEI Syntax
		$result = $model->validateLeiSyntax($model->getProperty('lei'));
		$this->assertTrue($result);
		// Invalide LEI Syntax
		$result = $model->validateLeiSyntax('');
		$this->assertFalse($result);
	}
*/

	/**
	 * CRUD (Create-Read-Update-Delete) Tests
	 */
/*
	public function testCRUD ()
	{
		$model = new HaHeLa\Modules_Model_Lei($this->dbh, $this->config, $this->options);
		//
		// Create (Crud)
		// 
		// Ein fake LEI-Datensatz wird in der Datenbank gespeichert. Die 
		// 	Identifikationsnummer des erzeugten LEI-Datensatzes wird in der 
		// 	Eigenschaft $id gespeichert und von der Methode create ausgebeben.
		$leiId = $model->setProperties($this->FAKE)->create();
		$this->assertIsInt($leiId);
		$this->assertGreaterThan(0, $leiId);
		//
		// Read (cRud)
		//
		// Der oben erzeugte fake LEI-Datensatz wird aus der Datenbank 
		// 	gelesen.
		//
		$model->read();
		// Holt die zusaetzlichen Objekteigenschaften,
		$leiProps = $model->getProperties();
		foreach ($leiProps as $key => $value) {
			if ($this->FAKE[$key] == $value) {
				$this->assertEquals($this->FAKE[$key], $value);
			}
			else {
				if ('updated_at' === $key) {
					$pattern = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
					$this->assertMatchesRegularExpression($pattern, $value);
				}
			}
		}
		//
		// Update (crUd)
		//
		$model->setProperty('legal_name', 'Wayne Industries');
		$result = $model->update();
		$this->assertTrue($result);
		$model->read();
		$legalName = $model->getProperty('legal_name');
		$this->assertEquals('Wayne Industries', $legalName);
		//
		// Delete (cruD)
		// 
		// Der oben erzeugte fake LEI-Datensatz wird in der Datenbank 
		// 	geloescht.
		$result = $model->delete();
		$this->assertTrue($result);
	}
*/		
}

