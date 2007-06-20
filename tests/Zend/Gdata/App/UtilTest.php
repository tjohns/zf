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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/App/Util.php';
require_once 'Zend/Gdata/App/Exception.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_App_UtilTest extends PHPUnit_Framework_TestCase
{

    public function testFormatTimestampFromString()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('2006-12-01');
        $this->assertEquals('2006-12-01', $date);
    }

    public function testFormatTimestampFromStringWithTimezone()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('2007-01-10T13:31:12-04:00');
        $this->assertEquals('2007-01-10T13:31:12-04:00', $date);
    }

    public function testFormatTimestampFromStringWithNonCompliantDate()
    {
        // assert that a correctly formatted timestamp is not modified
        $date = Zend_Gdata_App_Util::formatTimestamp('2007/07/13');
        $this->assertEquals('2007-07-13T00:00:00', $date);
    }

    public function testFormatTimestampFromInteger()
    {
        $ts = strtotime('2006-12-01');
        $date = Zend_Gdata_App_Util::formatTimestamp($ts);
        $this->assertEquals('2006-12-01T00:00:00', $date);
    }

    public function testExceptionFormatTimestampInvalid()
    {
        $util = new Zend_Gdata_App_Util();
        try {
            $ts = Zend_Gdata_App_Util::formatTimestamp('nonsense string');
        } catch (Zend_Gdata_App_Exception $e) {
            $this->assertEquals('Invalid timestamp: nonsense string.', $e->getMessage());
        }
    }

}
