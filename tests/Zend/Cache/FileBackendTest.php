<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/File.php';

/**
 * Common tests for backends
 */
require_once 'CommonBackendTest.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_FileBackendTest extends Zend_Cache_CommonBackendTest {
    
    protected $_instance;
    protected $_instance2;
    protected $_cacheDir;
    
    public function __construct()
    {
        parent::__construct('Zend_Cache_Backend_File');
    }
    
    public function setUp()
    {        
        $this->_cacheDir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $this->_instance = new Zend_Cache_Backend_File(array(
            'cacheDir' => $this->_cacheDir
        ));  
        parent::setUp();     
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_File(array());    
    }    
    
    public function testConstructorWithABadFileNamePrefix()
    {
        try {
            $class = new Zend_Cache_Backend_File(array(
                'fileNamePrefix' => 'foo bar'
            ));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testGetWithANonExistingCacheIdAndANullLifeTime() 
    {
        $this->_instance->setDirectives(array('lifeTime' => null));
        $this->assertFalse($this->_instance->load('barbar'));         
    }
    
    public function testSaveCorrectCallWithHashedDirectoryStructure()
    {
        $this->_instance->setOption('hashedDirectoryLevel', 2);
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertTrue($res);
    }
    
    public function testCleanModeAllWithHashedDirectoryStructure()
    {
        $this->_instance->setOption('hashedDirectoryLevel', 2);
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }
    
    public function testSaveWithABadCacheDir()
    {
        $this->_instance->setOption('cacheDir', '/foo/bar/lfjlqsdjfklsqd/');
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertFalse($res);
    }
    
}

?>
