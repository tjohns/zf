<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_RewriteRouter */
require_once 'Zend/Controller/RewriteRouter.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'PHPUnit/Runner/Version.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_RewriteRouterTest extends PHPUnit_Framework_TestCase
{
    protected $_router;
    
    public function setUp() {
        $this->_router = new Zend_Controller_RewriteRouter();
    }
    
    public function tearDown() {
        unset($this->_router);
    }

    public function testAddRoute()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $routes = $this->_router->getRoutes();

        $this->assertSame(1, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['archive']);

        $this->_router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));
        $routes = $this->_router->getRoutes();

        $this->assertSame(2, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['register']);
    }

    public function testAddRoutes()
    {
        $routes = array(
            'archive' => new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')),
            'register' => new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register'))
        );
        $this->_router->addRoutes($routes);

        $values = $this->_router->getRoutes();

        $this->assertSame(2, count($values));
        $this->assertType('Zend_Controller_Router_Route', $values['archive']);
        $this->assertType('Zend_Controller_Router_Route', $values['register']);
    }
    
    public function testHasRoute()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
    
        $this->assertSame(true, $this->_router->hasRoute('archive'));
        $this->assertSame(false, $this->_router->hasRoute('bogus'));
    }

    public function testGetRoute()
    {
        $archive = new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $this->_router->addRoute('archive', $archive);

        $route = $this->_router->getRoute('archive');
    
        $this->assertType('Zend_Controller_Router_Route', $route);
        $this->assertSame($route, $archive);
    }

    public function testRemoveRoute()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));

        $route = $this->_router->getRoute('archive');
        
        $this->_router->removeRoute('archive');
    
        $routes = $this->_router->getRoutes();
        $this->assertSame(0, count($routes));

        try {
            $route = $this->_router->removeRoute('archive');
        } catch (Zend_Controller_Router_Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
            return true;
        }

        $this->fail();
    }

    public function testGetNonExistentRoute()
    {
        try {
            $route = $this->_router->getRoute('bogus');
        } catch (Zend_Controller_Router_Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
            return true;
        }

        $this->fail();
    }

    public function testRoute()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request();
        
        $token = $this->_router->route($request);

        $this->assertType('Zend_Controller_Request_Http', $token);
    }

    public function testRouteWithIncorrectRequest()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request_Incorrect();
        
        try {
            $token = $this->_router->route($request);
            $this->fail('Should throw an Exception');
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
        }
    }
    
    public function testDefaultRoute()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request();

        $token = $this->_router->route($request);
        
        $routes = $this->_router->getRoutes();
        $this->assertType('Zend_Controller_Router_Route', $routes['default']);
    }

    public function testEmptyRoute()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/');
        
        $this->_router->addRoute('empty', new Zend_Controller_Router_Route(':year', array('year' => '2006')));
        
        $token = $this->_router->route($request);

        $this->assertSame('2006', $token->getParam('year'));
    }

    public function testRouteCompat()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/');
        
        $token = $this->_router->route($request);

        $this->assertSame(null, $token->getControllerName());
        $this->assertSame(null, $token->getActionName());
    }

    public function testRouteNotMatched()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/archive/action/bogus');

        $this->_router->addRoute('default', new Zend_Controller_Router_Route(':controller/:action'));
        
        $token = $this->_router->route($request);

        $this->assertNull($token->getControllerName());
        $this->assertNull($token->getActionName());
    }

    public function testDefaultRouteMatched()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/ctrl/act');

        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }

    public function testDefaultRouteMatchedWithModules()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/mod/ctrl/act');
        $this->_router->setParam('useModules', true);
        
        $token = $this->_router->route($request);
        
        $this->assertSame('mod', $token->getParam('module')); // getModuleName does not exist yet
        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }
    
    public function testDefaultRouteMatchedWithControllerOnly()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/ctrl');

        $token = $this->_router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('index', $token->getActionName());
    }

    public function testFirstRouteMatched()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/archive/2006');

        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $this->_router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $token = $this->_router->route($request);

        $this->assertSame('archive', $token->getControllerName());
        $this->assertSame('show', $token->getActionName());
    }

    public function testGetCurrentRoute()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/ctrl/act');

        try {
            $route = $this->_router->getCurrentRoute();
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
        }
        
        try {
            $route = $this->_router->getCurrentRouteName();
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
        }
        
        $token = $this->_router->route($request);

        try {
            $route = $this->_router->getCurrentRoute();
            $name = $this->_router->getCurrentRouteName();
        } catch (Exception $e) {
            $this->fail('Current route is not set');
        }
        
        $this->assertSame('default', $name);
        $this->assertType('Zend_Controller_Router_Route', $route);
    }
    
    public function testAddConfig()
    {
        require_once 'Zend/Config/Ini.php';
        $file = dirname(__FILE__) . '/_files/routes.ini';
        $config = new Zend_Config_Ini($file, 'testing');
        
        $this->_router->addConfig($config, 'routes');
        
        $this->assertType('Zend_Controller_Router_StaticRoute', $this->_router->getRoute('news'));
        $this->assertType('Zend_Controller_Router_Route', $this->_router->getRoute('archive'));
        
        try {
            $this->_router->addConfig($config, 'database');
        } catch (Exception $e) {
            $this->assertType('Zend_Controller_Router_Exception', $e);
            return true;
        }
        
        $this->fail();
        
    }
    
    public function testRemoveDefaultRoutes()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/ctrl/act');
        $this->_router->removeDefaultRoutes();

        $token = $this->_router->route($request);

        $routes = $this->_router->getRoutes();
        $this->assertSame(0, count($routes));
    }
    
    
    /* Param tests copied from Front Controller. Functionality is exactly the same */
    
    public function testGetSetParam()
    {
        $this->_router->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_router->getParam('foo'));

        $this->_router->setParam('bar', 'baz');
        $this->assertEquals('baz', $this->_router->getParam('bar'));
    }

    public function testGetSetParams()
    {
        $this->_router->setParams(array('foo' => 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->_router->getParams());

        $this->_router->setParam('baz', 'bat');
        $this->assertSame(array('foo' => 'bar', 'baz' => 'bat'), $this->_router->getParams());

        $this->_router->setParams(array('foo' => 'bug'));
        $this->assertSame(array('foo' => 'bug', 'baz' => 'bat'), $this->_router->getParams());
    }

    public function testClearParams()
    {
        $this->_router->setParams(array('foo' => 'bar', 'baz' => 'bat'));
        $this->assertSame(array('foo' => 'bar', 'baz' => 'bat'), $this->_router->getParams());

        $this->_router->clearParams('foo');
        $this->assertSame(array('baz' => 'bat'), $this->_router->getParams());

        $this->_router->clearParams();
        $this->assertSame(array(), $this->_router->getParams());

        $this->_router->setParams(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'), $this->_router->getParams());
        $this->_router->clearParams(array('foo', 'baz'));
        $this->assertSame(array('bar' => 'baz'), $this->_router->getParams());
    }

}

/**
 * Zend_Controller_RouterTest_Request - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_RewriteRouterTest_Request extends Zend_Controller_Request_Http
{
    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://localhost/foo/bar/baz/2';
        }

        parent::__construct($uri);
    }
}

/**
 * Zend_Controller_RouterTest_Request_Incorrect - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_RewriteRouterTest_Request_Incorrect extends Zend_Controller_Request_Abstract
{
}
