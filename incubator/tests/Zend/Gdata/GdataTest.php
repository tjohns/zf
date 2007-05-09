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
        $client = $gdata->getHttpClient();
        $this->assertTrue($client instanceof Zend_Http_Client,
            'Expecting object of type Zend_Http_Client, got '
            . (gettype($client) == 'object' ? get_class($client) : gettype($client))
        );
    }

    public function testSpecificHttpClient()
    {
        $client = new Zend_Http_Client();
        $gdata = new Zend_Gdata($client);
        $client2 = $gdata->getHttpClient();
        $this->assertTrue($client2 instanceof Zend_Http_Client,
            'Expecting object of type Zend_Http_Client, got '
            . (gettype($client) == 'object' ? get_class($client) : gettype($client))
        );
        $this->assertSame($client, $client2);
    }

    public function testExceptionNotHttpClient()
    {
        $obj = new ArrayObject();
        try {
            $gdata = new Zend_Gdata($obj);
            $this->fail('Expecting to catch Zend_Gdata_App_HttpException');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Gdata_App_HttpException'),
                'Expecting Zend_Gdata_App_HttpException, got '.get_class($e));
            $this->assertEquals('Argument is not an instance of Zend_Http_Client.', $e->getMessage());
        }
    }

}
