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
 * Zend_Http_Client_Adapter_Test 
 */
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 *
 */
class Zend_Feed_ImportTest extends PHPUnit_Framework_TestCase
{
    protected $_client;
    
    protected $_feedDir;
    
    /**
     * HTTP client test adapter
     *
     * @var Zend_Http_Client_Adapter_Test
     */
    protected $_adapter;

    public function setUp()
    {
    	$this->_adapter = new Zend_Http_Client_Adapter_Test();
        Zend_Feed::setHttpClient(new Zend_Http_Client(null, array('adapter' => $this->_adapter)));
        $this->_client = Zend_Feed::getHttpClient();
        $this->_feedDir = dirname(__FILE__) . '/_files';
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
    	$response = new Zend_Http_Response(200, array(), file_get_contents("$this->_feedDir/$filename"));
    	$this->_adapter->setResponse($response);

        $feed = Zend_Feed::import('http://localhost');
        $this->assertTrue($feed instanceof Zend_Feed_Atom);
    }

    protected function _importRssValid($filename)
    {
    	$response = new Zend_Http_Response(200, array(), file_get_contents("$this->_feedDir/$filename"));
    	$this->_adapter->setResponse($response);

        $feed = Zend_Feed::import('http://localhost');
        $this->assertTrue($feed instanceof Zend_Feed_Rss);
    }

    protected function _importInvalid($filename)
    {
    	$response = new Zend_Http_Response(200, array(), file_get_contents("$this->_feedDir/$filename"));
    	$this->_adapter->setResponse($response);
    	
        try {
            $feed = Zend_Feed::import('http://localhost');
        } catch (Exception $e) {
        }
        $this->assertTrue($e instanceof Zend_Feed_Exception, 'Expected Zend_Feed_Exception to be thrown');
    }
}
