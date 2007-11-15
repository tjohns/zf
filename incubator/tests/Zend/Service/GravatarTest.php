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
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Service_Gravatar */
require_once 'Zend/Service/Gravatar.php';

/** Zend_Http_Client_Adapter_Test */
require_once 'Zend/Http/Client/Adapter/Test.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package 	Zend_Service
 * @subpackage  UnitTests
 */
class Zend_Service_GravatarTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gravatar = new Zend_Service_Gravatar('email@example.com');

        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        Zend_Service_Gravatar::setHttpClient($client);

        $this->defaultParams = array(
        'rating'  => 'G',
        'size'    => '80',
        'default' => 'http://www.gravatar.com/avatar.php',
        'border'  => '',
        );
    }

    public function testEmail()
    {
        $this->assertEquals('email@example.com', $this->gravatar->getEmail());
        $this->gravatar->setEmail('another@example.com');
        $this->assertEquals('another@example.com', $this->gravatar->getEmail());
    }

    public function testParams()
    {
        $this->assertEquals($this->defaultParams, $this->gravatar->getParams());
        $params = array(
        'rating'  => 'R',
        'size'    => '75',
        'default' => 'http://www.example.com/avatar.jpg',
        'border'  => 'FF0000',
        );
        $this->gravatar->setParams($params);
        $this->assertEquals($params, $this->gravatar->getParams());
    }

    public function testGravatarId() 
    {
        $this->gravatar->setEmail('email@example.com');
        $this->assertEquals(md5('email@example.com'), $this->gravatar->getGravatarId());
    }

    public function testUri()
    {
        $uri = 'http://www.gravatar.com/avatar.php?gravatar_id=5658ffccee7f0ebfda2b226238b1eb6e&rating=G&size=80&default=http%3A%2F%2Fwww.gravatar.com%2Favatar.php&border=';

        $this->assertEquals($uri, $this->gravatar->getUri());
    }

    public function testIsValid()
    {
        $response = "HTTP/1.1 200 OK\r\n"
                  . "Connection: close\r\n"
                  . "Content-type: image/jpeg\r\n"
                  . 'Etag: "1723090581"\r\n'
                  . "Accept-ranges: bytes\r\n"
                  . "Last-modified: Sun, 08 Apr 2007 09:34:46 GMT\r\n"
                  . "Content-length: 1787\r\n"
                  . "Date: Mon, 06 Aug 2007 18:02:49 GMT\r\n"
                  . "Server: lighttpd/1.4.13\r\n"
                  . "\r\n"
                  . "image here";

        $this->adapter->setResponse($response);

        $this->assertTrue($this->gravatar->isValid());

        $response = "HTTP/1.1 301 Moved Permanently\r\n"
                  . "Connection: close\r\n"
                  . "Location: http://www.gravatar.com/avatar.php\r\n"
                  . 'Content-length: 0\r\n'
                  . "Date: Mon, 06 Aug 2007 18:08:58 GMT\r\n"
                  . "\r\n";

        $this->adapter->setResponse($response);

        $this->assertFalse($this->gravatar->isValid());
    }

}
