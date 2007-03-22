<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Flickr_FlickrTest extends PHPUnit_Framework_TestCase
{
    /**
     * Reference to Flickr service consumer object
     *
     * @var Zend_Service_Flickr
     */
    protected $_flickr;

    /**
     * Path to test data files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend_Http_Client_Adapter_Test
     */
    protected $_httpClientAdapterTest;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $_httpClientAdapterSocket;

    /**
     * Flickr API key
     *
     * @var string
     */
    protected $_apiKey = 'd6f50aed387bee5dc5bae945a49e7436';

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        /**
         * @see Zend_Service_Flickr
         */
        require_once 'Zend/Service/Flickr.php';
        $this->_flickr = new Zend_Service_Flickr($this->_apiKey);

        $this->_filesPath = dirname(__FILE__) . '/_files';

        /**
         * @see Zend_Http_Client_Adapter_Socket
         */
        require_once 'Zend/Http/Client/Adapter/Socket.php';
        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

        /**
         * @see Zend_Http_Client_Adapter_Test
         */
        require_once 'Zend/Http/Client/Adapter/Test.php';
        $this->_httpClientAdapterTest = new Zend_Http_Client_Adapter_Test();
    }

    /**
     * Basic testing to ensure that tagSearch() works as expected
     *
     * @return void
     */
    public function testTagSearchBasic()
    {
        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $options = array(
            'per_page' => 10,
            'page'     => 1,
            'tag_mode' => 'or',
            'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
            );

        $resultSet = $this->_flickr->tagSearch('php', $options);

        $this->assertEquals(4285, $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        $this->assertEquals(0, $resultSet->key());

        try {
            $resultSet->seek(-1);
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->seek(9);

        try {
            $resultSet->seek(10);
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->rewind();

        $resultSetIds = array(
            '428222530',
            '427883929',
            '427884403',
            '427887192',
            '427883923',
            '427884394',
            '427883930',
            '427884398',
            '427883924',
            '427884401'
            );

        $this->assertTrue($resultSet->valid());

        foreach ($resultSetIds as $resultSetId) {
            $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__ . "-result_$resultSetId"));
            $result = $resultSet->current();
            $this->assertTrue($result instanceof Zend_Service_Flickr_Result);
            $resultSet->next();
        }

        $this->assertFalse($resultSet->valid());
    }

    /**
     * Utility method that saves an HTTP response to a file
     *
     * @param  string $name
     * @return void
     */
    protected function _saveResponse($name)
    {
        file_put_contents("$this->_filesPath/$name.response",
                          $this->_flickr->getRestClient()->getHttpClient()->getLastResponse()->asString());
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function _loadResponse($name)
    {
        return file_get_contents("$this->_filesPath/$name.response");
    }
}

