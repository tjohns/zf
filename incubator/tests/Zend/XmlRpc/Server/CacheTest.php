<?php
/**
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 */

/**
 * Zend_XmlRpc_Server
 */
require_once 'Zend/XmlRpc/Server.php';

/**
 * Zend_XmlRpc_Server_Cache
 */
require_once 'Zend/XmlRpc/Server/Cache.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * PHPUnit Incomplete Test Exception; use to mark tests that must be skipped 
 * due to missing permissions
 */
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Zend_XmlRpc_Server test class; for access to test classes
 */
require_once dirname(__FILE__) . '/../ServerTest.php';

/**
 * Test case for Zend_XmlRpc_Server_Cache
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 */
class Zend_XmlRpc_Server_CacheTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Server object
     * @var Zend_XmlRpc_Server
     */
    protected $_server;

    /**
     * Location of cache file
     * @var string 
     */
    protected $_file;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_file = realpath(dirname(__FILE__)) . '/xmlrpc.cache';
        $this->_server = new Zend_XmlRpc_Server();
        $this->_server->setClass('zxrs_test_methods', 'domain1');
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        if (file_exists($this->_file)) {
            unlink($this->_file);
        }
        unset($this->_server);
    }

    /**
     * Test functionality of both get() and save()
     */
    public function testGetSave()
    {
        // Remove this line once the test has been written
        if (!is_writeable('./')) {
            throw new PHPUnit_Framework_IncompleteTestError('Directory not writeable');
        }

        $this->assertTrue(Zend_XmlRpc_Server_Cache::save($this->_file, $this->_server));
        $expected = $this->_server->getCallbacks();
        $server = new Zend_XmlRpc_Server();
        $this->assertTrue(Zend_XmlRpc_Server_Cache::get($this->_file, $server));
        $actual = $server->getCallbacks();

        $diff = array_diff($expected, $actual);
        $this->assertTrue(empty($diff));
    }

    /**
     * Zend_XmlRpc_Server_Cache::delete() test
     */
    public function testDelete()
    {
        // Remove this line once the test has been written
        if (!is_writeable('./')) {
            throw new PHPUnit_Framework_IncompleteTestError('Directory not writeable');
        }

        $this->assertTrue(Zend_XmlRpc_Server_Cache::save($this->_file, $this->_server));
        $this->assertTrue(Zend_XmlRpc_Server_Cache::delete($this->_file));
    }


}
