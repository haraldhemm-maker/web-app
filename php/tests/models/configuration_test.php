<?php


/**
 * PHP unit tests of the HaHeLa\ConfigurationModel class 
 *  - Model zur Konfiguration des PHP-Systems.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration configuration.xml
 */


declare(strict_types=1);
use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'configuration.php';


// models
require_once '..'.DS.'modules'.DS.'model'.DS.'configuration.php';


/**
 * FileModel Test
 */
class ConfigurationTest extends TestCase
{
    /**
     * Konstanten
     */
    // In der folgenden Sektion befinden sich vertrauliche Daten.
    public const CONFIDENTIAL_CONFIG_SECTION = 'DATABASE';
    
    /**
     * Erzeuge eine Instanz der Klasse ConfigurationModel.
     */
    public function testCreateConfigurationObject()
    {
        $model = new HaHeLa\ConfigurationModel();
        $this->assertTrue($model instanceof HaHeLa\ConfigurationModel);
        $this->assertInstanceOf('HaHeLa\ConfigurationModel', $model);
    }
    
    
    /**
     * Lese nur die NICHT vertraulichen Konfigurationsdaten.
     */
    public function testReadNotConfidentialData()
    {
        // Nutzung der Voreinstellung
        $model = new HaHeLa\ConfigurationModel();
        $data1 = $model->read();
        $this->assertIsArray($data1);
        // Nutzung des Parameters $notConfidential
        $model = new HaHeLa\ConfigurationModel($notConfidential = TRUE);
        $data2 = $model->read();
        $this->assertIsArray($data2);
        // Vergleich der beiden Arrays auf Gleichheit
        $this->assertTrue($data1 === $data2);
    }
    
    
    /**
     * Lese alle Konfigurationsdaten, auch die vertraulichen.
     */
    public function testReadAllData()
    {
        $model = new HaHeLa\ConfigurationModel();
        $data = $model->read($notConfidential = FALSE);
        $this->assertIsArray($data);
        $this->assertTrue(
            array_key_exists(self::CONFIDENTIAL_CONFIG_SECTION, $data)
        );
    }
    
}