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
 * @see Zend_Service_Technorati_BlogInfoResult
 */
require_once 'Zend/Service/Technorati/BlogInfoResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_BlogInfoResultTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->dom = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileContentAsDom('TestBlogInfoResult.xml');
        $this->object = new Zend_Service_Technorati_BlogInfoResult($this->dom);
    }

    public function testConstruct()
    {
        try {
            $object = new Zend_Service_Technorati_BlogInfoResult($this->dom);
            $this->assertType('Zend_Service_Technorati_BlogInfoResult', $object);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
    }

    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        try {
            $object = new Zend_Service_Technorati_BlogInfoResult('foo');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            $this->assertContains("DOMDocument", $e->getMessage());
        }
    }

    public function testBlogInfoResult()
    {
        // check valid object
        $this->assertNotNull($this->object);
        $object = $this->object;

        // check weblog
        $weblog = $object->getWeblog();
        $this->assertType('Zend_Service_Technorati_Weblog', $weblog);
        $this->assertEquals('Simone Carletti\'s Blog', $weblog->getName());

        // check url
        $this->assertType('Zend_Uri_Http', $object->getUrl());
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog'), $object->getUrl());

        // check inboundblogs
        $this->assertType('integer', $object->getInboundBlogs());
        $this->assertEquals(86, $object->getInboundBlogs());
    
        // check inboundlinks
        $this->assertType('integer', $object->getInboundLinks());
        $this->assertEquals(114, $object->getInboundLinks());
    }
    
    public function testBlogInfoResultUrlWithInvalidSchemaEqualsToWeblogUrl()
    {
        $dom = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileContentAsDom('TestBlogInfoResult.xml');
        try {
            $object = new Zend_Service_Technorati_BlogInfoResult($dom);
            $this->assertType('Zend_Service_Technorati_BlogInfoResult', $object);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
        
        // check url
        $this->assertType('Zend_Uri_Http', $object->getUrl());
        $this->assertEquals($object->getWeblog()->getUrl(), $object->getUrl());
    }
}
