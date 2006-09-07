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
class Zend_Cache_sqliteBackendTest extends Zend_Cache_CommonBackendTest {
    
    protected $_instance;
    private $_cacheDir;
    
    public function __construct()
    {
        parent::__construct('Zend_Cache_Backend_Sqlite');
    }
    
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
        parent::setUp();     
    }
    
    public function tearDown()
    {
        $this->_instance->___dropDatabaseFile();
        parent::tearDown();
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_Sqlite(array('cacheDBCompletePath' => $this->_cacheDir . 'cache.db'));    
    }
    
    public function testConstructorWithABadDBPath()
    {
        try {
            $test = new Zend_Cache_Backend_Sqlite(array('cacheDBCompletePath' => '/foo/bar/lfjlqsdjfklsqd/cache.db'));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }
    
}

?>
