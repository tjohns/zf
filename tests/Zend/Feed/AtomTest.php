<?php
/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */


/**
 * Zend_Feed_Atom
 */
require_once 'Zend/Feed/Atom.php';

/**
 * Zend_HttpClient_File
 */
require_once 'Zend/HttpClient/File.php';

/**
 * PHPUnit2 Test Case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 *
 */
class Zend_Feed_AtomTest extends PHPUnit2_Framework_TestCase
{
    protected $_client;
    protected $_feedDir;

    public function setUp()
    {
        Zend_Feed::setHttpClient(new Zend_HttpClient_File());
        $this->_client = Zend_Feed::getHttpClient();

        $this->_feedDir = 'Zend/Feed/_files';
    }

    public function testGoogle()
    {
        $this->_testValid('AtomTestGoogle.xml');
    }

    public function testMozillazine()
    {
        $this->_testValid('AtomTestMozillazine.xml');
    }

    public function testOReilly()
    {
        $this->_testValid('AtomTestOReilly.xml');
    }

    public function testPlanetPHP()
    {
        $this->_testValid('AtomTestPlanetPHP.xml');
    }

    public function testSample1()
    {
        $this->_testValid('AtomTestSample1.xml');
    }

    public function testSample2()
    {
        $this->_testValid('AtomTestSample2.xml');
    }

    public function testSample3()
    {
        $this->_testInvalid('AtomTestSample3.xml');
    }

    public function testSample4()
    {
        $this->_testValid('AtomTestSample4.xml');
    }

    protected function _testValid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = new Zend_Feed_Atom('http');
        } catch (Exception $e) {
            $this->fail("$filename - " . $e->getMessage());
        }
    }

    protected function _testInvalid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = new Zend_Feed_Atom('http');
        } catch (Exception $e) {
        }
        $this->assertTrue($e instanceof Zend_Feed_Exception, 'Expected Zend_Feed_Exception to be thrown');
    }

}
