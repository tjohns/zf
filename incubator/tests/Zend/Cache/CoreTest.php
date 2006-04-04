<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Core.php';
require_once 'Zend/Cache/Backend/File.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_CoreTest extends PHPUnit2_Framework_TestCase {
    
    private $_instance;
    
    public function setUp()
    {
        if (!$this->_instance) $this->_instance = new Zend_Cache_Core(array());
    }
    
    public function tearDown()
    {
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Core(array('lifeTime' => 3600, 'caching' => true));
    }
    
    public function testConstructorBadCall()
    {
        try {
            $test = new Zend_Cache_Core('foo');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');    
    }
    
    public function testConstructorBadOption()
    {
        try {
            $test = new Zend_Cache_Core(array('foo' => 'bar', 'lifeTime' => 3600));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetBackendCorrectCall()
    {
        $backend = new Zend_Cache_Backend_File(array());
        $this->_instance->setBackend($backend);
    }
    
    public function testSetBackendBadCall()
    {
        try {
            $this->_instance->setBackend('foo');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetOptionCorrectCall()
    {
        $this->_instance->setOption('lifeTime', 1200);
    }
       
    public function testSetOptionBadCall()
    {
        try {
            $this->_instance->setOption(array('lifeTime'), 1200);        
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetOptionUnknownOption()
    {
        try {
            $this->_instance->setOption('foo', 1200);        
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    // TODO :
    // save()
    // clean()
    // get()
    // test()
    // remove()
    
}

?>
