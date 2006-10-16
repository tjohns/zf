<?php
require_once 'Zend/Http/Request.php';
require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'PHPUnit2/Framework/IncompleteTestError.php';

class Zend_Http_RequestTest extends PHPUnit2_Framework_TestCase 
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
        $this->assertEquals('var1=val1&var2=val2', $this->_request->getQuery());
    }
 
    public function testSetQuery()
    {
        $this->_request->setQuery('var1=val2&var2=val3&var3=val1');
        $this->assertEquals('var1=val2&var2=val3&var3=val1', $this->_request->getQuery());
    }
 
    public function testSetGetPost()
    {
        $this->_request->setPost('post1=val1&post2=val2');
        $this->assertEquals('post1=val1&post2=val2', $this->_request->getPost());
    }
 
    public function testGetPathInfo()
    {
        $this->assertEquals('/news/3', $this->_request->getPathInfo());
    }
 
    public function testSetPathInfo()
    {
        $this->_request->setPathInfo('/archives/past/4');
        $this->assertEquals('/archives/past/4', $this->_request->getPathInfo());
    }
 
    public function testGetAlias()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testSetAlias()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testGetAliases()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testGetRequestUri()
    {
        $this->assertEquals('/news/3?var1=val1&var2=val2', $this->getRequestUri());
    }
 
    public function testSetRequestUri()
    {
        $this->_request->setRequestUri('/archives/past/4?set=this&unset=that');
        $this->assertEquals('/archives/past/4?set=this&unset=that', $this->_request->getRequestUri());
        $this->assertEquals('/archives/past/4', $this->_request->getPathInfo());
        $this->assertEquals('set=this&unset=that', $this->_request->getQuery());
    }
 
    public function testGetBaseUrl()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testSetBaseUrl()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testGetBasePath()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testSetBasePath()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testGetCookie()
    {
        throw new PHPUnit2_Framework_IncompleteTestError('not implemented');
    }
 
    public function testGetServer()
    {
        $this->assertEquals($_SERVER, $this->_request->getServer());
    }
 
    public function testGetEnv()
    {
        $this->assertEquals($_ENV, $this->_request->getEnv());
    }
}
