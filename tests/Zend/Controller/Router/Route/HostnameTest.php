<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route_Hostname */
require_once 'Zend/Controller/Router/Route/Hostname.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_HostnameTest extends PHPUnit_Framework_TestCase
{

    public function testCorrectHostMatch()
    {
        $route = $this->_getHostRoute();

        $_SERVER['HTTP_HOST'] = 'www.google.com';
        $values = $route->match('foo/bar');
        $this->assertEquals('ctrl', $values['controller']);

        $route = $this->_getHostRoute();
        $_SERVER['SERVER_NAME'] = 'www.google.com';
        $_SERVER['SERVER_PORT'] = 80;
        $values = $route->match('foo/bar');
        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testWrongHostMatch()
    {
        $route = $this->_getHostRoute();

        $_SERVER['HTTP_HOST'] = 'foo.google.com';
        $values = $route->match('foo/bar');
        $this->assertFalse($values);

        $route = $this->_getHostRoute();
        $_SERVER['SERVER_NAME'] = 'foo.google.com';
        $_SERVER['SERVER_PORT'] = 80;
        $values = $route->match('foo/bar');
        $this->assertFalse($values);
    }

    public function testRegexHostMatch()
    {
        $route = $this->_getHostRegexRoute();
        $_SERVER['HTTP_HOST'] = 'foo.google.com';
        $values = $route->match('foo/bar');
        $this->assertEquals('foo', $values['subdomain']);
    }

    public function testWrongRegexHostMatch()
    {
        $route = $this->_getHostRoute();

        $_SERVER['HTTP_HOST'] = 'foo.microsoft.com';
        $values = $route->match('foo/bar');
        $this->assertFalse($values);
    }

    public function testAssembleHost()
    {
        $route = $this->_getHostRoute();

        $_SERVER['HTTP_HOST']   = 'www.google.com';
        $_SERVER['SERVER_PORT'] = '80';
        $url = $route->assemble(array('bar' => 'bar'));
        $this->assertEquals('http://www.google.com/foo/bar', $url);

        $route = $this->_getHostRoute();
        $_SERVER['SERVER_NAME'] = 'www.google.com';
        $_SERVER['SERVER_PORT'] = 80;
        $url = $route->assemble(array('bar' => 'bar'));
        $this->assertEquals('http://www.google.com/foo/bar', $url);
    }

    public function testAssembleHttpsRoute()
    {
        $route = $this->_getHostRoute();

        $_SERVER['HTTP_HOST']   = 'www.google.com';
        $_SERVER['SERVER_PORT'] =  443;
        $_SERVER['HTTPS']       = 'on';
        $url = $route->assemble(array('bar' => 'bar'));
        $this->assertEquals('https://www.google.com/foo/bar', $url);
    }

    public function testAssembleHttpsAndPortHostRoute()
    {
        // Clean host env
        unset($_SERVER['HTTP_HOST'],
            $_SERVER['HTTPS'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']);

        $route = array(
            'host' => 'www.google.com:200',
            'path' => 'foo/:bar'
        );
        $route = new Zend_Controller_Router_Route($route, array('controller' => 'ctrl', 'action' => 'act'));
        $route->setRequest(new Zend_Controller_Request_Http());

        $_SERVER['HTTP_HOST']   = 'www.google.com:200';
        $_SERVER['SERVER_PORT'] = 200;
        $_SERVER['HTTPS']       = 'on';
        $url = $route->assemble(array('bar' => 'bar'));
        $this->assertEquals('https://www.google.com:200/foo/bar', $url);
    }

    public function testAssembleHttpsOffHostRoute()
    {
        $route = $this->_getHostRoute();
        $_SERVER['HTTP_HOST']   = 'www.google.com';
        $_SERVER['HTTPS']       = 'off';
        $url = $route->assemble(array('bar' => 'bar'));
        $this->assertEquals('http://www.google.com/foo/bar', $url);
    }

    public function testAssembleRegexHostRoute()
    {
        $route = $this->_getHostRegexRoute();

        $_SERVER['HTTP_HOST']   = 'www.google.com';
        $url = $route->assemble(array('bar' => 'bar', 'subdomain' => 'foo'));
        $this->assertEquals('http://foo.google.com/foo/bar', $url);
    }

    protected function _getHostRoute()
    {
        // Clean host env
        unset($_SERVER['HTTP_HOST'],
            $_SERVER['HTTPS'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']);

        $route = array(
            'host' => 'www.google.com',
            'path' => 'foo/:bar'
        );
        $route = new Zend_Controller_Router_Route($route, array('controller' => 'ctrl', 'action' => 'act'));
        $route->setRequest(new Zend_Controller_Request_Http());

        return $route;
    }

    protected function _getHostRegexRoute()
    {
        // Clean host env
        unset($_SERVER['HTTP_HOST'],
            $_SERVER['HTTPS'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']);

        $route = array(
            'host' => array(
                'regex'   => '(.*?)\.google\.com',
                'reverse' => '%s.google.com',
                'params'  => array(
                    1 => 'subdomain'
                )
            ),
            'path' => 'foo/:bar'
        );
        $route = new Zend_Controller_Router_Route($route, array('controller' => 'ctrl', 'action' => 'act'));
        $route->setRequest(new Zend_Controller_Request_Http());

        return $route;
    }

}
