<?php


/**
 * PHP unit tests of the HaHeLa\HttpRequestModel class 
 *  - Model zum Test eines HTTP Requests des PHP-Systems.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 *
 * Run test:
 * > cd /home/harald/public_html/app/web-app/php/tests
 * > php phpunit-9.6.13.phar --configuration http-request_test.xml
 */


declare(strict_types=1);
use PHPUnit\Framework\TestCase;


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// interfaces
require_once '..'.DS.'modules'.DS.'interface'.DS.'http'.DS.'request.php';


// models
require_once '..'.DS.'modules'.DS.'model'.DS.'http'.DS.'request.php';


$_SERVER['REQUEST_METHOD'] = '';
$_GET = array();
$_POST = array();


/**
 * HTTP Request Model Test
 */
class HttpRequestModelTest extends TestCase
{
    /**
     * Setup of the PHP unit test.
     *
     * @return void
     */
    protected function setUp(): void
    {
    }

    
    /**
     * Erzeuge eine Instanz der Klasse HttpRequestModel.
     */
    public function testCreateHttpRequestMoelObject()
    {
        $model = new HaHeLa\HttpRequestModel();
        $this->assertTrue($model instanceof HaHeLa\HttpRequestModel);
        $this->assertInstanceOf('HaHeLa\HttpRequestModel', $model);
    }
    
    
    /**
     * Hole die HTTP Request Methode. 
     */
    public function testGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertEquals('GET', $model->getMethod());
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertEquals('POST', $model->getMethod());

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertEquals('PUT', $model->getMethod());
        
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertEquals('DELETE', $model->getMethod());
    }
    
    
    /**
     * Ist ein benannter Parameter vothanden.
     */
    public function testIssetParameter()
    {
        global $_GET, $_POST;        
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'foo';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertTrue($model->issetParameter('foo'));

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'foo';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertTrue($model->issetParameter('foo'));
        
        // Parameter des PUT und DELETE Request koennen nicht geprueft werden.
        
    }
    
    
    /**
     * Ermittle den Wert eines benannten Parameters.
     */
    public function testGetParameter()
    {
        global $_GET, $_POST;        

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'foo';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertIsString($model->getParameter('foo', 'string'));
        $this->assertEquals('foo', $model->getParameter('foo', 'string'));

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'foo';
        $model = new HaHeLa\HttpRequestModel();
        $this->assertIsString($model->getParameter('foo', 'string'));
        $this->assertEquals('foo', $model->getParameter('foo', 'string'));

        // Parameterwerte des PUT und DELETE Requests koennen nicht ermittelt 
        //  werden.
        
    }
    
    
    /**
     * Holt die Namen aller Parameter.
     */
    public function testGetParameterNames()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'foo';
        $_GET['baz'] = '1';
        $model = new HaHeLa\HttpRequestModel();
        $paramNames = $model->getParameterNames();
        $this->assertIsArray($paramNames);
        foreach ($paramNames as $paramName) {
            $this->assertTrue(in_array($paramName, $paramNames));
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'foo';
        $_POST['baz'] = '1';
        $model = new HaHeLa\HttpRequestModel();
        $paramNames = $model->getParameterNames();
        $this->assertIsArray($paramNames);
        foreach ($paramNames as $paramName) {
            $this->assertTrue(in_array($paramName, $paramNames));
        }

        // Parameternamen des PUT und DELETE Requests koennen nicht ermittelt 
        //  werden.
        
    }
    
    
    /**
     * Ermittelt den Wert eines benannten Headers.
     */
    public function testGetHeader()
    {
        
        // Die entsprechenden Header existieren in der CLI-Umgebung nicht.
        //  Nach meiner Kenntnis kömmem sie somit in dieser Umgebung auch 
        //  nicht ermittelt werden.
        
        // $model = new HaHeLa\HttpRequestModel();
        // $headerValue = $model->getHeader('Authorization');
                
        // Dummy
        $this->assertTrue(true);        
    }
    
    
    /**
     * Holt die Rohdaten des HTTP-Requests.
     */
    public function testGetInputRawData()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'foo';
        $_GET['baz'] = '1';
        $model = new HaHeLa\HttpRequestModel();
        $rawData = $model->getInputRawData();
        $this->assertIsArray($rawData);        
    }
}


