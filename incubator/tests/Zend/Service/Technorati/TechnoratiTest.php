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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TechnoratiTestHelper.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_TechnoratiTest extends PHPUnit_Framework_TestCase
{
    const TEST_APYKEY = 'somevalidapikey';
    const TEST_GETINFO_USERNAME = 'weppos';

    public function setUp()
    {
        /**
         * @see Zend_Http_Client_Adapter_Test
         */
        require_once 'Zend/Http/Client/Adapter/Test.php';
        $adapter = new Zend_Http_Client_Adapter_Test();

        /**
         * @see Zend_Http_Client
         */
        require_once 'Zend/Http/Client.php';
        $client = new Zend_Http_Client(Zend_Service_Technorati::URI_BASE, array(
            'adapter' => $adapter
        ));
        
        $this->technorati = new Zend_Service_Technorati(self::TEST_APYKEY);
        $this->adapter = $adapter;
        $this->technorati->getRestClient()->setHttpClient($client);
    }

    public function testConstruct()
    {
        try {
            $object = new Zend_Service_Technorati(self::TEST_APYKEY);
            $this->assertType('Zend_Service_Technorati', $object);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
    }

    public function testApiKeyMatches()
    {
        $object = $this->technorati;
        $this->assertEquals(self::TEST_APYKEY, $object->getApiKey());
    }

    public function testSetGetApiKey()
    {
        $object = $this->technorati;

        $set = 'just a test';
        $get = $object->setApiKey($set)->getApiKey();
        $this->assertEquals($set, $get);
    }

    /**
     * @todo
     */
    public function testCosmos()
    {
    }

    public function testGetInfo()
    {
        $result = $this->_setResponseFromFile('TestGetInfoSuccess.xml')->getInfo(self::TEST_GETINFO_USERNAME);

        $this->assertType('Zend_Service_Technorati_GetInfoResult', $result);
        // content is validated in Zend_Service_Technorati_GetInfoResult tests
    }

    public function testGetInfoThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestGetInfoError.xml')->getInfo(self::TEST_GETINFO_USERNAME);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            // exception message must match response message
            $this->assertContains("Username is a required field.", $e->getMessage());
        }
    }

    public function testGetInfoThrowsExceptionWithInvalidOptionUsername()
    {
        // username is mandatory --> validated by PHP interpreter
        // username must not be empty
        try {
            $result = $this->technorati->getInfo('');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            // exception message must match response message
            $this->assertContains("'username'", $e->getMessage());
        }
    }

    public function testGetInfoThrowsExceptionWithInvalidOptionFormat()
    {
        // format must be XML
        try {
            $result = $this->technorati->getInfo(self::TEST_GETINFO_USERNAME, 
                                                array('format' => 'rss'));
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            // exception message must match response message
            $this->assertContains("'format'", $e->getMessage());
        }
    }
    
    public function testGetInfoThrowsExceptionWithUnknownOption()
    {
        try {
            $result = $this->technorati->getInfo(self::TEST_GETINFO_USERNAME, 
                                                array('foo' => 'bar'));
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            // exception message must match response message
            $this->assertContains("'foo'", $e->getMessage());
        }
    }
    
    public function testKeyInfo()
    {
        $result = $this->_setResponseFromFile('TestKeyInfo.xml')->keyInfo();

        $this->assertType('Zend_Service_Technorati_KeyInfoResult', $result);
        // content is validated in Zend_Service_Technorati_KeyInfoResult tests
    }

    public function testKeyInfoThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestKeyInfoError.xml')->keyInfo();
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            // exception message must match response message
            $this->assertContains("Invalid key.", $e->getMessage());
        }
    }
    
    /**
     * Loads a response content from a test case file
     * and sets the content to current Test Adapter.
     * 
     * Returns current Zend_Service_Technorati instance
     * to let developers use the powerful chain call.
     * 
     * Do not execute any file validation. Please use this method carefully.
     * 
     * @params  string $file
     * @return  Zend_Service_Technorati
     */
    private function _setResponseFromFile($file) 
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "Date: " . date(DATE_RFC1123) . "\r\n"
                  . "Server: Apache\r\n"
                  . "Cache-Control: max-age=60\r\n"
                  . "Content-Type: text/xml; charset=UTF-8\r\n"
                  . "X-Powered-By: PHP/5.2.1\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . file_get_contents(dirname(__FILE__) . '/_files/' . $file) ;

        $this->adapter->setResponse($response);
        return $this->technorati; // allow chain call
     }
}
