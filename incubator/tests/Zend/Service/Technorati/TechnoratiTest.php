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
    const TEST_COSMOS_URL = 'http://www.simonecarletti.com/blog/';
    const TEST_DAILYCOUNT_QUERY = 'google';
    const TEST_GETINFO_USERNAME = 'weppos';
    const TEST_BLOGINFO_URL = 'http://www.simonecarletti.com/blog/';
    const TEST_BLOGPOSTTAGS_URL = 'http://www.simonecarletti.com/blog/';
    
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
            $this->fail("Exception " . $e->getMessage() . " thrown");
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

    public function testCosmos()
    {
        $result = $this->_setResponseFromFile('TestCosmosSuccess.xml')->cosmos(self::TEST_COSMOS_URL);

        $this->assertType('Zend_Service_Technorati_CosmosResultSet', $result);
        $this->assertEquals(2, $result->totalResultsReturned);
        $this->assertType('Zend_Service_Technorati_CosmosResult', $result->seek(0));
        // content is validated in Zend_Service_Technorati_CosmosResultSet tests
        // $this->assertEquals(2, count($result)); // TODO: failed?
    }

    public function testCosmosThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestCosmosError.xml')->cosmos(self::TEST_COSMOS_URL);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    }

    public function testCosmosThrowsExceptionWithInvalidUrl()
    {
        // url is mandatory --> validated by PHP interpreter
        // url must not be empty
        $this->_testThrowsExceptionWithInvalidUrl('blogPostTags');
    }

    public function testCosmosThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('type'      => 'foo'),
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
            // 'current'    =>  // cast to int
            // 'claim'      =>  // cast to int
            // 'highlight'  =>  // cast to int
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestCosmosSuccess.xml', 'cosmos', array(self::TEST_COSMOS_URL));
    }

    public function testCosmosOption()
    {
        $options = array(
            array('type'      => 'link'),
            array('type'      => 'weblog'),
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
            array('current'   => false),   // cast to int
            array('current'   => 0),       // cast to int
            array('claim'     => false),   // cast to int
            array('claim'     => 0),       // cast to int
            array('highlight' => false),   // cast to int
            array('highlight' => 0),       // cast to int
        );
        $this->_testOption($options, 'TestCosmosSuccess.xml', 'cosmos', array(self::TEST_COSMOS_URL));
    }

    public function testDailyCounts()
    {
        $result = $this->_setResponseFromFile('TestDailyCountsSuccess.xml')->dailyCounts(self::TEST_DAILYCOUNT_QUERY);

        $this->assertType('Zend_Service_Technorati_DailyCountsResultSet', $result);
        $this->assertEquals(180, $result->totalResultsReturned);
        $this->assertType('Zend_Service_Technorati_DailyCountsResult', $result->seek(0));
        // content is validated in Zend_Service_Technorati_DailyCountsResultSet tests
    }

    public function testDailyCountsThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestDailyCountsError.xml')->dailyCounts(self::TEST_DAILYCOUNT_QUERY);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Missing required parameter", $e->getMessage());
        }
    }

    public function testDailyCountsThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('days' => 0),
            array('days' => '0'),
            array('days' => 181),
            array('days' => '181'),
            );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestDailyCountsSuccess.xml', 'dailyCounts', array(self::TEST_DAILYCOUNT_QUERY));
    }
    
    public function testDailyCountsOption()
    {
        $options = array(
            array('days' => 120),   // cast to int
            array('days' => '120'), // cast to int
            array('days' => 180),   // cast to int
            array('days' => '180'), // cast to int
            );
        $this->_testOption($options, 'TestDailyCountsSuccess.xml', 'dailyCounts', array(self::TEST_DAILYCOUNT_QUERY));
    }
    
    public function testBlogInfo()
    {
        $result = $this->_setResponseFromFile('TestBlogInfoSuccess.xml')->blogInfo(self::TEST_BLOGINFO_URL);

        $this->assertType('Zend_Service_Technorati_BlogInfoResult', $result);
        // content is validated in Zend_Service_Technorati_BlogInfoResult tests
    }

    public function testBlogInfoThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestBlogInfoError.xml')->blogInfo(self::TEST_BLOGINFO_URL);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    }
    
    public function testBlogInfoThrowsExceptionWithInvalidUrl()
    {
        // url is mandatory --> validated by PHP interpreter
        // url must not be empty
        $this->_testThrowsExceptionWithInvalidUrl('blogInfo');
    }
    
    public function testBlogInfoThrowsExceptionWithUrlNotWeblog()
    {
        // emulate Technorati exception
        // when URL is not a recognized weblog
        try {
            $result = $this->_setResponseFromFile('TestBlogInfoErrorUrlNotWeblog.xml')->blogInfo('www.simonecarletti.com');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Technorati weblog", $e->getMessage());
        }
    }
    
    public function testBlogPostTags()
    {
        $result = $this->_setResponseFromFile('TestBlogPostTagsSuccess.xml')->blogPostTags(self::TEST_BLOGPOSTTAGS_URL);

        $this->assertType('Zend_Service_Technorati_TagsResult', $result);
        // content is validated in Zend_Service_Technorati_TagsResult tests
    }

    public function testBlogPostTagsThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestBlogPostTagsError.xml')->blogPostTags(self::TEST_BLOGPOSTTAGS_URL);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    }

    public function testBlogPostTagsThrowsExceptionWithInvalidUrl()
    {
        // url is mandatory --> validated by PHP interpreter
        // url must not be empty
        $this->_testThrowsExceptionWithInvalidUrl('blogPostTags');
    }
    
    public function testBlogPostTagsThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestBlogPostTagsSuccess.xml', 'blogPostTags', array(self::TEST_BLOGPOSTTAGS_URL));
    }

    public function testBlogPostTagsOption()
    {
        $options = array(
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
        );
        $this->_testOption($options, 'TestBlogPostTagsSuccess.xml', 'blogPostTags', array(self::TEST_BLOGPOSTTAGS_URL));
    }
    
    public function testTopTags()
    {
        $result = $this->_setResponseFromFile('TestTopTagsSuccess.xml')->topTags();

        $this->assertType('Zend_Service_Technorati_TagsResult', $result);
        // content is validated in Zend_Service_Technorati_TagsResult tests
    }

    public function testTopTagsThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestTopTagsError.xml')->topTags();
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid key.", $e->getMessage());
        }
    }

    public function testTopTagsThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestTopTagsSuccess.xml', 'topTags');
    }

    public function testTopTagsOption()
    {
        $options = array(
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
        );
        $this->_testOption($options, 'TestTopTagsSuccess.xml', 'topTags');
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
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Username is a required field.", $e->getMessage());
        }
    }

    public function testGetInfoThrowsExceptionWithInvalidUsername()
    {
        // username is mandatory --> validated by PHP interpreter
        // username must not be empty
        try {
            $result = $this->technorati->getInfo('');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("'username'", $e->getMessage());
        }
    }

    public function testKeyInfo()
    {
        $result = $this->_setResponseFromFile('TestKeyInfoSuccess.xml')->keyInfo();

        $this->assertType('Zend_Service_Technorati_KeyInfoResult', $result);
        // content is validated in Zend_Service_Technorati_KeyInfoResult tests
    }

    public function testKeyInfoThrowsExceptionWithError()
    {
        try {
            $result = $this->_setResponseFromFile('TestKeyInfoError.xml')->keyInfo();
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid key.", $e->getMessage());
        }
    }

    public function testAllThrowsExceptionWithInvalidOptionFormat()
    {
        $invalidFormatOption = array('format' => 'rss');
        // format must be XML
        $methods = array('cosmos'       => self::TEST_COSMOS_URL,
                         'dailyCounts'  => self::TEST_DAILYCOUNT_QUERY,
                         'topTags'      => null,
                         'blogInfo'     => self::TEST_BLOGINFO_URL,
                         'blogPostTags' => self::TEST_BLOGPOSTTAGS_URL,
                         'getInfo'      => self::TEST_GETINFO_USERNAME);
        $technorati = $this->technorati;
        
        foreach ($methods as $method => $param) {
            $options = array_merge((array) $param, array($invalidFormatOption));
            try {
                $result = call_user_func_array(array(&$technorati, $method), $options);
                $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
            } catch (Zend_Service_Technorati_Exception $e) {
                $this->assertContains("'format'", $e->getMessage());
            }
        }
    }

    public function testAllThrowsExceptionWithUnknownOption()
    {
        $invalidOption = array('foo' => 'bar');
        $methods = array('cosmos'       => self::TEST_COSMOS_URL,
                         'dailyCounts'  => self::TEST_DAILYCOUNT_QUERY,
                         'topTags'      => null,
                         'blogInfo'     => self::TEST_BLOGINFO_URL,
                         'blogPostTags' => self::TEST_BLOGPOSTTAGS_URL,
                         'getInfo'      => self::TEST_GETINFO_USERNAME);

        $technorati = $this->technorati;
        foreach ($methods as $method => $param) {
            $options = array_merge((array) $param, array($invalidOption));
            try {
                $result = call_user_func_array(array(&$technorati, $method), $options);
                $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
            } catch (Zend_Service_Technorati_Exception $e) {
                $this->assertContains("'foo'", $e->getMessage());
            }
        }
    }
    
    /**
     * Tests whether $callbackMethod method throws an Exception
     * with Invalid Url.
     * 
     * @param   string $callbackMethod
     */
    private function _testThrowsExceptionWithInvalidUrl($callbackMethod)
    {
        try {
            $result = $this->technorati->$callbackMethod('');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("'url'", $e->getMessage());
        }
    }
    
    /**
     * Tests whether for each $validOptions a method call is successful.
     * 
     * @param   array $validOptions
     * @param   string $xmlFile
     * @param   string $callbackMethod
     * @param   null|array $callbackRequiredOptions
     */
    private function _testOption($validOptions, $xmlFile, $callbackMethod, $callbackRequiredOptions = null)
    {
        $technorati = $this->_setResponseFromFile($xmlFile);
        foreach ($validOptions as $pair) {
            list($option, $value) = each($pair);
            $options = is_array($callbackRequiredOptions) ? 
                            array_merge($callbackRequiredOptions, array($pair)) :
                            array($pair);
            
            try {
                $result = call_user_func_array(
                            array(&$technorati, $callbackMethod),
                            $options
                          );
                /**
                 * @todo    validate converted value
                 */
            } catch (Zend_Service_Technorati_Exception $e) {
                $this->fail("Exception " . $e->getMessage() . " thrown " .
                            "for option '$option' value '$value'");
            }
        }
    }
    
    /**
     * Tests whether for each $validOptions a method call is successful.
     * 
     * @param   array $invalidOptions
     * @param   string $xmlFile
     * @param   string $callbackMethod
     * @param   null|array $callbackRequiredOptions
     */
    private function _testThrowsExceptionWithInvalidOption($invalidOptions, $xmlFile, $callbackMethod, $callbackRequiredOptions = null)
    {
        $technorati = $this->_setResponseFromFile($xmlFile);
        foreach ($invalidOptions as $pair) {
            list($option, $value) = each($pair);
            $options = is_array($callbackRequiredOptions) ? 
                            array_merge($callbackRequiredOptions, array($pair)) :
                            array($pair);
            
            try {
                $result = call_user_func_array(
                            array(&$technorati, $callbackMethod),
                            $options
                          );
                $this->fail("Expected Zend_Service_Technorati_Exception not thrown " .
                            "for option '$option' value '$value'");
            } catch (Zend_Service_Technorati_Exception $e) {
                $this->assertContains("'$option'", $e->getMessage());
            }
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
