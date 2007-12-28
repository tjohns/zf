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
 * @see Zend_Service_Technorati_GetInfoResult
 */
require_once 'Zend/Service/Technorati/GetInfoResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_GetInfoResultTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->dom = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileContentAsDom('TestGetInfoResult.xml');
        $this->object = new Zend_Service_Technorati_GetInfoResult($this->dom);
    }

    public function testConstruct()
    {
        try {
            $object = new Zend_Service_Technorati_GetInfoResult($this->dom);
            $this->assertType('Zend_Service_Technorati_GetInfoResult', $object);
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
            $object = new Zend_Service_Technorati_GetInfoResult('foo');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            $this->assertContains("DOMDocument", $e->getMessage());
        }
    }

    public function testGetInfoResult()
    {
        // check valid object
        $this->assertNotNull($this->object);
        $object = $this->object;

        // check author
        $author = $object->getAuthor();
        $this->assertType('Zend_Service_Technorati_Author', $author);
        $this->assertEquals('weppos', $author->getUsername());

        // check weblogs
        $weblogs = $object->getWeblogs();
        $this->assertType('array', $weblogs);
        $this->assertEquals(2, count($weblogs));
        $this->assertType('Zend_Service_Technorati_Weblog', $weblogs[0]);
    }
}
