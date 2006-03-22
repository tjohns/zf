<?php
/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */


/**
 * Zend_Feed_Rss
 */
require_once 'Zend/Feed/Rss.php';

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
class Zend_Feed_RssTest extends PHPUnit2_Framework_TestCase
{
    protected $_client;
    protected $_feedDir;

    public function setUp()
    {
        Zend_Feed::setHttpClient(new Zend_HttpClient_File());
        $this->_client = Zend_Feed::getHttpClient();

        $this->_feedDir = 'Zend/Feed/_files';
    }

    public function testHarvardLaw()
    {
        $this->_testValid('RssTestHarvardLaw.xml');
    }

    public function testPlanetPHP()
    {
        $this->_testValid('RssTestPlanetPHP.xml');
    }

    public function testSlashdot()
    {
        $this->_testValid('RssTestSlashdot.xml');
    }

    public function testCNN()
    {
        $this->_testValid('RssTestCNN.xml');
    }

    public function test091Sample1()
    {
        $this->_testValid('RssTest091Sample1.xml');
    }

    public function test092Sample1()
    {
        $this->_testValid('RssTest092Sample1.xml');
    }

    public function test100Sample1()
    {
        $this->_testValid('RssTest100Sample1.xml');
    }

    public function test100Sample2()
    {
        $this->_testValid('RssTest100Sample2.xml');
    }

    public function test200Sample1()
    {
        $this->_testValid('RssTest200Sample1.xml');
    }

    protected function _testValid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = new Zend_Feed_Rss('http');
        } catch (Exception $e) {
            $this->fail("$filename - " . $e->getMessage());
        }
    }

    protected function _testInvalid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = new Zend_Feed_Rss('http');
        } catch (Exception $e) {
        }
        $this->assertTrue($e instanceof Zend_Feed_Exception, 'Expected Zend_Feed_Exception to be thrown');
    }

}
