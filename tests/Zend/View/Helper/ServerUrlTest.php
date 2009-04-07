<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/View/Helper/ServerUrl.php';

/**
 * Tests Zend_View_Helper_ServerUrl
 */
class Zend_View_Helper_ServerUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Back up of $_SERVER
     *
     * @var array
     */
    protected $_serverBackup;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_serverBackup    = $_SERVER;
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SERVER = $this->_serverBackup;
    }

    public function testServerUrlWithNonStandardPort()
    {
        // Non standard port
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com:8888', $url->serverUrl());

        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $this->assertEquals('http://example.com:8888', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com:8888', $url->serverUrl());

        $this->assertEquals('http://example.com:8888/test', $url->serverUrl('/test'));
    }

    public function testServerUrlWithNonStandardPortSecure()
    {
        // Non standard port
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8888', $url->serverUrl());

        $_SERVER['HTTPS']     = true;
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8888', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8888', $url->serverUrl());

        $this->assertEquals('https://example.com:8888/test', $url->serverUrl('/test'));
    }

    public function testServerUrlWithStandardPort()
    {
        // Non standard port
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());

        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '80';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());

        $this->assertEquals('http://example.com/test', $url->serverUrl('/test'));
    }

    public function testServerUrlWithStandardPortSecure()
    {
        // Non standard port
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());

        $_SERVER['HTTPS'] = true;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '443';
        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());

        $this->assertEquals('https://example.com/test', $url->serverUrl('/test'));
    }
}
