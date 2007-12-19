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
    const TEST_APYKEY = 'qwergvcxweqrtyhgfbfvdcsaQ';

    public function setUp()
    {
        $this->_object = new Zend_Service_Technorati(self::TEST_APYKEY);
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
        $object = $this->_object;
        $this->assertEquals(self::TEST_APYKEY, $object->getApiKey());
    }

    public function testSetGetApiKey()
    {
        $object = $this->_object;

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

    /**
     * @todo
     */
    public function testGetInfo()
    {
    }

    /**
     * @todo
     */
    public function testKeyInfo()
    {
    }
}
