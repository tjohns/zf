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

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_GdataTest extends PHPUnit_Framework_TestCase
{

    public function testDefaultHttpClient()
    {
        $gdata = new Zend_Gdata();
        $this->assertTrue($gdata->getHttpClient() instanceof Zend_Http_Client);
    }

    public function testSpecificHttpClient()
    {
        $client = new Zend_Http_Client();
        $gdata = new Zend_Gdata($client);
        $this->assertSame($client, $gdata->getHttpClient());
    }

    public function testExceptionNotHttpClient()
    {
        $obj = new ArrayObject();
        try {
            $gdata = new Zend_Gdata($obj);
        } catch (Zend_Http_Exception $e) {
            $this->assertEquals('Argument is not an instance of Zend_Http_Client.', $e->getMessage());
        }
    }

    public function testFormatTimestampFromString()
    {
        $gdata = new Zend_Gdata();
        $date = $gdata->formatTimestamp('2006-12-01');
        $this->assertEquals('2006-12-01T00:00:00', $date);
    }

    public function testFormatTimestampFromInteger()
    {
        $gdata = new Zend_Gdata();
        $ts = strtotime('2006-12-01');
        $date = $gdata->formatTimestamp($ts);
        $this->assertEquals('2006-12-01T00:00:00', $date);
    }

    public function testExceptionFormatTimestampInvalid()
    {
        $gdata = new Zend_Gdata();
        try {
            $ts = $gdata->formatTimestamp('nonsense string');
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals('Invalid timestamp: nonsense string.', $e->getMessage());
        }
    }

}
