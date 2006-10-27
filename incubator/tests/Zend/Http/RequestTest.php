<?php
require_once 'Zend/Http/Request.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

class Zend_Http_RequestTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_Http_Request
     */
    protected $_request;

    public function setUp()
    {
        $this->_request = new Zend_Http_Request('http://framework.zend.com/news/3?var1=val1&var2=val2#anchor');
    }

    public function tearDown()
    {
        unset($this->_request);
    }

    public function testIsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->_request->isPost());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->_request->isPost());
    }

    public function testGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', $this->_request->getMethod());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('GET', $this->_request->getMethod());
    }
 
    public function testGetQuery()
    {
        $this->assertEquals('val1', $this->_request->getQuery('var1'));
    }
 

    public function testGetPost()
    {
        $_POST['post1'] = 'val1';
        $this->assertEquals('val1', $this->_request->getPost('post1'));
    }
 
    public function testGetPathInfo()
    {
        $this->assertEquals('/news/3', $this->_request->getPathInfo(), var_export($this->_request->getBaseUrl(), 1));
    }
 
    public function testSetPathInfo()
    {
        $this->_request->setPathInfo('/archives/past/4');
        $this->assertEquals('/archives/past/4', $this->_request->getPathInfo());
    }

    public function testPathInfoNeedingBaseUrl()
    {
        $request = new Zend_Http_Request('http://localhost/test/index.php/ctrl-name/act-name');
        $this->assertEquals('/test/index.php/ctrl-name/act-name', $request->getRequestUri());
        $request->setBaseUrl('/test/index.php');
        $this->assertEquals('/test/index.php', $request->getBaseUrl());

        $requestUri = $request->getRequestUri();
        $baseUrl    = $request->getBaseUrl();
        $pathInfo   = substr($requestUri, strlen($baseUrl));
        $this->assertTrue($pathInfo ? true : false);

        $this->assertEquals('/ctrl-name/act-name', $request->getPathInfo(), "Expected $pathInfo;");
    }
 
    public function testGetSetAlias()
    {
        $this->_request->setAlias('controller', 'var1');
        $this->assertEquals('var1', $this->_request->getAlias('controller'));
    }
 
    public function testGetAliases()
    {
        $this->_request->setAlias('controller', 'var1');
        $this->_request->setAlias('action', 'var2');
        $this->assertSame(array('controller' => 'var1', 'action' => 'var2'), $this->_request->getAliases());
    }
 
    public function testGetRequestUri()
    {
        $this->assertEquals('/news/3?var1=val1&var2=val2', $this->_request->getRequestUri());
    }
 
    public function testSetRequestUri()
    {
        $this->_request->setRequestUri('/archives/past/4?set=this&unset=that');
        $this->assertEquals('/archives/past/4?set=this&unset=that', $this->_request->getRequestUri());
        $this->assertEquals('/archives/past/4', $this->_request->getPathInfo());
        $this->assertEquals('this', $this->_request->getQuery('set'));
        $this->assertEquals('that', $this->_request->getQuery('unset'));
    }
 
    public function testGetBaseUrl()
    {
        $this->assertSame('', $this->_request->getBaseUrl());
    }
 
    public function testSetBaseUrl()
    {
        $this->_request->setBaseUrl('/news');
        $this->assertEquals('/news', $this->_request->getBaseUrl());
    }
 
    public function testGetSetBasePath()
    {
        $this->_request->setBasePath('/news');
        $this->assertEquals('/news', $this->_request->getBasePath());
    }
 
    public function testGetCookie()
    {
        $_COOKIE['foo'] = 'bar';
        $this->assertSame('bar', $this->_request->getCookie('foo'));
    }
 
    public function testGetServer()
    {
        $this->assertEquals($_SERVER['REQUEST_METHOD'], $this->_request->getServer('REQUEST_METHOD'));
    }
 
    public function testGetEnv()
    {
        $this->assertEquals($_ENV['PATH'], $this->_request->getEnv('PATH'));
    }
}
