<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/Sqlite.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_sqliteBackendTest extends PHPUnit2_Framework_TestCase {
    
    private $_instance;
    private $_cacheDir;
    
    public function setUp()
    {        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->_cacheDir = $this->_getTmpDirWindows() . DIRECTORY_SEPARATOR;
        } else {
            $this->_cacheDir = $this->_getTmpDirUnix() . DIRECTORY_SEPARATOR;
        }
        $this->_instance = new Zend_Cache_Backend_Sqlite(array(
            'cacheDBCompletePath' => $this->_cacheDir . 'cache.db'
        ));
        $this->_instance->save('bar : data to cache', 'bar', array('tag3', 'tag4'));
    }
    
    private function _getTmpDirWindows()
    {
        if (isset($_ENV['TEMP'])) {
            return $_ENV['TEMP'];
        }
        if (isset($_ENV['TMP'])) {
            return $_ENV['TMP'];
        }
        if (isset($_ENV['windir'])) {
            return $_ENV['windir'] . '\\temp';
        }
        if (isset($_ENV['SystemRoot'])) {
            return $_ENV['SystemRoot'] . '\\temp';
        }
        if (isset($_SERVER['TEMP'])) {
            return $_SERVER['TEMP'];
        }
        if (isset($_SERVER['TMP'])) {
            return $_SERVER['TMP'];
        }
        if (isset($_SERVER['windir'])) {
            return $_SERVER['windir'] . '\\temp';
        }
        if (isset($_SERVER['SystemRoot'])) {
            return $_SERVER['SystemRoot'] . '\\temp';
        }
        return '\temp';
    }
    
    private function _getTmpDirUnix()
    {
        if (isset($_ENV['TMPDIR'])) {
	        return $_ENV['TMPDIR'];
	    }
	    if (isset($_SERVER['TMPDIR'])) {
	        return $_SERVER['TMPDIR'];
	    }
	    return '/tmp';
    }
    
    public function tearDown()
    {
        //$this->_instance->clean('all');
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_Sqlite(array('cacheDBCompletePath' => $this->_cacheDir . 'cache.db'));    
    }
    
    public function testConstructorBadCall1()
    {
        try {
            $test = new Zend_Cache_Backend_Sqlite('foo');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');    
    }
    
    public function testConstructorBadCall2()
    {
        try {
            $test = new Zend_Cache_Backend_Sqlite(array());
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');    
    }  
     
    public function testConstructorBadOption()
    {
        try {
            $test = new Zend_Cache_Backend_Sqlite(array('foo' => 'bar'));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetDirectivesCorrectCall()
    {
        $this->_instance->setDirectives(array('lifeTime' => 3600, 'logging' => true));
    }
    
    public function testSetDirectivesBadCall()
    {
        try {
            $this->_instance->setDirectives('foo');
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSetDirectivesBadDirective()
    {
        // A bad directive (not known by a specific backend) is possible
        // => so no exception here
        $this->_instance->setDirectives(array('foo' => true, 'lifeTime' => 3600));
    }
    
    public function testSetDirectivesBadDirective2()
    {
        try {
            $this->_instance->setDirectives(array('foo' => true, 12 => 3600));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testSaveCorrectCall()
    {
        $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
    }
    
    public function testSaveCorrectCallWithNullLifeTime()
    {
        $this->_instance->setDirectives(array('lifeTime' => null));
        $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
    }
    
    public function testRemoveCorrectCall()
    {
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar')); 
        $this->assertFalse($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));        
    }
    
    public function testTestCorrectCall1()
    {
        $res = $this->_instance->test('bar');
        if (!$res) {
            $this->fail('test() return false');
        }
        if (!($res > 999999)) {
            $this->fail('test() return an incorrect integer');
        }
        return;    
    }
    
    public function testTestCorrectCall2() 
    {    
        $this->assertFalse($this->_instance->test('barbar'));       
    }
         
    public function testTestCorrectCall3()
    {
        $this->_instance->setDirectives(array('lifeTime' => null));
        $res = $this->_instance->test('bar');
        if (!$res) {
            $this->fail('test() return false');
        }
        if (!($res > 999999)) {
            $this->fail('test() return an incorrect integer');
        }
        return;
    }
    
    // TODO : get()
    // TODO : clean()
    
}

?>
