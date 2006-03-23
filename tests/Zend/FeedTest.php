<?php
/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */


/**
 * Zend_Feed
 */
require_once 'Zend/Feed.php';

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
class Zend_FeedTest extends PHPUnit2_Framework_TestCase
{
    protected $_client;
    protected $_feedDir;

    public function setUp()
    {
        Zend_Feed::setHttpClient(new Zend_HttpClient_File());
        $this->_client = Zend_Feed::getHttpClient();

        $this->_feedDir = 'Zend/Feed/_files';
    }

    public function testAtomGoogle()
    {
        $this->_importAtomValid('AtomTestGoogle.xml');
    }

    public function testAtomMozillazine()
    {
        $this->_importAtomValid('AtomTestMozillazine.xml');
    }

    public function testAtomOReilly()
    {
        $this->_importAtomValid('AtomTestOReilly.xml');
    }

    public function testAtomPlanetPHP()
    {
        $this->_importAtomValid('AtomTestPlanetPHP.xml');
    }

    public function testAtomSample1()
    {
        $this->_importAtomValid('AtomTestSample1.xml');
    }

    public function testAtomSample2()
    {
        $this->_importAtomValid('AtomTestSample2.xml');
    }

    public function testAtomSample3()
    {
        $this->_importInvalid('AtomTestSample3.xml');
    }

    public function testAtomSample4()
    {
        $this->_importAtomValid('AtomTestSample4.xml');
    }

    public function testRssHarvardLaw()
    {
        $this->_importRssValid('RssTestHarvardLaw.xml');
    }

    public function testRssPlanetPHP()
    {
        $this->_importRssValid('RssTestPlanetPHP.xml');
    }

    public function testRssSlashdot()
    {
        $this->_importRssValid('RssTestSlashdot.xml');
    }

    public function testRssCNN()
    {
        $this->_importRssValid('RssTestCNN.xml');
    }

    public function testRss091Sample1()
    {
        $this->_importRssValid('RssTest091Sample1.xml');
    }

    public function testRss092Sample1()
    {
        $this->_importRssValid('RssTest092Sample1.xml');
    }

    public function testRss100Sample1()
    {
        $this->_importRssValid('RssTest100Sample1.xml');
    }

    public function testRss100Sample2()
    {
        $this->_importRssValid('RssTest100Sample2.xml');
    }

    public function testRss200Sample1()
    {
        $this->_importRssValid('RssTest200Sample1.xml');
    }

    protected function _importAtomValid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = Zend_Feed::import('http');
            $this->assertTrue($feed instanceof Zend_Feed_Atom);
        } catch (Exception $e) {
            $this->fail("$filename - " . $e->getMessage());
        }
    }

    protected function _importRssValid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = Zend_Feed::import('http');
            $this->assertTrue($feed instanceof Zend_Feed_Rss);
        } catch (Exception $e) {
            $this->fail("$filename - " . $e->getMessage());
        }
    }

    protected function _importInvalid($filename)
    {
        $this->_client->setFilename("$this->_feedDir/$filename");
        try {
            $feed = Zend_Feed::import('http');
        } catch (Exception $e) {
        }
        $this->assertTrue($e instanceof Zend_Feed_Exception, 'Expected Zend_Feed_Exception to be thrown');
    }

}
