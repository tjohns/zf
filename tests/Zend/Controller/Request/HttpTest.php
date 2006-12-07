<?php
require_once 'Zend/Controller/Request/Http.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_Controller_Request_HttpTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Original request URI
     * @var string 
     */
    protected $_origRequestUri = '';

    public function setUp()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->_origRequestUri = $_SERVER['REQUEST_URI'];
        }
        $this->_request = new Zend_Controller_Request_Http();
        $this->_request = new Zend_Controller_Request_Http('http://framework.zend.com/news/3?var1=val1&var2=val2#anchor');
    }

    public function tearDown()
    {
        unset($this->_request);
        $_SERVER['REQUEST_URI'] = $this->_origRequestUri;
    }

    public function testSetGetControllerKey()
    {
        $this->_request->setControllerKey('controller');
        $this->assertEquals('controller', $this->_request->getControllerKey());

        $this->_request->setControllerKey('foo');
        $this->assertEquals('foo', $this->_request->getControllerKey());
    } 

    public function testSetGetActionKey()
    {
        $this->_request->setActionKey('action');
        $this->assertEquals('action', $this->_request->getActionKey());

        $this->_request->setActionKey('foo');
        $this->assertEquals('foo', $this->_request->getActionKey());
    } 

    public function testSetGetControllerName()
    {
        $this->_request->setControllerName('foo');
        $this->assertEquals('foo', $this->_request->getControllerName());

        $this->_request->setControllerName('bar');
        $this->assertEquals('bar', $this->_request->getControllerName());
    }
 
    public function testSetGetActionName()
    {
        $this->_request->setActionName('foo');
        $this->assertEquals('foo', $this->_request->getActionName());

        $this->_request->setActionName('bar');
        $this->assertEquals('bar', $this->_request->getActionName());
    }
 
    public function testSetGetParam()
    {
        $this->_request->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_request->getParam('foo'));
    }
 
    public function testSetGetParams()
    {
        $params = array(
            'foo' => 'bar',
            'boo' => 'bah',
            'fee' => 'fi'
        );
        $this->_request->setParams($params);
        $received = $this->_request->getParams();
        $this->assertSame($params, array_intersect_assoc($params, $received));
    }

    public function testConstructSetsRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/mycontroller/myaction?foo=bar';
        $request = new Zend_Controller_Request_Http();
        $this->assertEquals('/mycontroller/myaction', $request->getPathInfo());
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

        $this->assertEquals('foo', $this->_request->getQuery('BAR', 'foo'));

        $expected = array('var1' => 'val1', 'var2' => 'val2');
        $this->assertEquals( $expected, $this->_request->getQuery());
    }
 

    public function testGetPost()
    {
        $_POST['post1'] = 'val1';
        $this->assertEquals('val1', $this->_request->getPost('post1'));

        $this->assertEquals('foo', $this->_request->getPost('BAR', 'foo'));

        $_POST['post2'] = 'val2';
        $expected = array('post1' => 'val1', 'post2' => 'val2');
        $this->assertEquals($expected, $this->_request->getPost());

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
        $request = new Zend_Controller_Request_Http('http://localhost/test/index.php/ctrl-name/act-name');
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
        $this->assertEquals('foo', $this->_request->getCookie('BAR', 'foo'));
        $this->assertEquals($_COOKIE, $this->_request->getCookie());
    }
 
    public function testGetServer()
    {
        $this->assertEquals($_SERVER['REQUEST_METHOD'], $this->_request->getServer('REQUEST_METHOD'));
        $this->assertEquals('foo', $this->_request->getServer('BAR', 'foo'));
        $this->assertEquals($_SERVER, $this->_request->getServer());
    }
 
    public function testGetEnv()
    {
        if (isset($_ENV['PATH'])) {
            $this->assertEquals($_ENV['PATH'], $this->_request->getEnv('PATH'));
        }
        $this->assertEquals('foo', $this->_request->getEnv('BAR', 'foo'));
        $this->assertEquals($_ENV, $this->_request->getEnv());
    }
}
