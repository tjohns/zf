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
 * @version    $Id: TagsResultTest.php 7253 2007-12-24 13:34:35Z weppos $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TechnoratiTestHelper.php';

/**
 * @see Zend_Service_Technorati_TagsResult
 */
require_once 'Zend/Service/Technorati/TagResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_TagResultTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->domElements = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileElementsAsDom('TestTagResultSet.xml');
    }

    public function testConstruct()
    {
        try {
            $object = new Zend_Service_Technorati_TagResult($this->domElements->item(0));
            $this->assertType('Zend_Service_Technorati_TagResult', $object);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
    }

    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        if (Zend_Service_Technorati_TechnoratiTestHelper::skipInvalidArgumentTypeTests()) {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
            return;
        }
        
        try {
            $object = new Zend_Service_Technorati_TagResult('foo');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            $this->assertContains("DOMElement", $e->getMessage());
        }
    }

    public function testTagResult()
    {
        $object = new Zend_Service_Technorati_TagResult($this->domElements->item(1));
        
        // check properties
        $this->assertType('string', $object->getTitle());
        $this->assertContains('Permalink for : VerveEarth', $object->getTitle());
        $this->assertType('string', $object->getExcerpt());
        $this->assertContains('VerveEarth: Locate Your Blog!', $object->getExcerpt());
        // @todo Zend_Uri_Http
        $this->assertType('string', $object->getPermalink());
        $this->assertEquals('http://scienceroll.com/2007/11/14/verveearth-locate-your-blog/', $object->getPermalink());
        // @todo Zend_Date
        $this->assertType('string', $object->getCreated());
        $this->assertEquals('2007-11-14 21:52:11', $object->getCreated());
        $this->assertType('string', $object->getUpdated());
        $this->assertEquals('2007-11-14 21:57:59', $object->getUpdated());
        
        // check weblog
        $this->assertType('Zend_Service_Technorati_Weblog', $object->getWeblog());
        $this->assertEquals(' ScienceRoll', $object->getWeblog()->getName());
    }
}
