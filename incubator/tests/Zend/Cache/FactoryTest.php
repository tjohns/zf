<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

//ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../..');

/**
 * Zend_HttpClient
 */
require_once 'Zend/Cache.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_HttpClient
 * @subpackage UnitTests
 */
class Zend_Cache_FactoryTest extends PHPUnit2_Framework_TestCase
{

    public function setUp()
    {
    }
    
    public function tearDown()
    {
    }
    
    public function testAvailableFrontends()
    {
        $this->assertType('array', Zend_Cache::$availableFrontends);
    }
    
    public function testAvailableBackends()
    {
        $this->assertType('array', Zend_Cache::$availableBackends);
    }
    
    public function testFactoryCorrectCall()
    {
        $generated_frontend = Zend_Cache::factory('Core', 'File');
        $this->assertEquals('Zend_Cache_Core', get_class($generated_frontend));
        
        $generated_backend = $this->getNonPublicProperty($generated_frontend, '_backend');
        $this->assertEquals('Zend_Cache_Backend_File', get_class($generated_backend));
    }

}
