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

    public function testDefaultRoute()
    {
        $routes = $this->_router->getRoutes();
        $this->assertType('Zend_Controller_Router_Route', $routes['default']);
    }

    public function testGetRoute()
    {
        $route = $this->_router->getRoute('default');
        $routes = $this->_router->getRoutes();
    
        $this->assertType('Zend_Controller_Router_Route', $route);
        $this->assertSame($route, $routes['default']);
    }

    public function testRemoveRoute()
    {
        $route = $this->_router->getRoute('default');
        
        $this->_router->removeRoute('default');
    
        $routes = $this->_router->getRoutes();
        $this->assertSame(0, count($routes));

        try {
            $route = $this->_router->getRoute('default');
        } catch (Zend_Controller_Router_Exception $e) {
            return true;
        }

        $this->fail();
    }

    public function testGetNonExistentRoute()
    {
        try {
            $route = $this->_router->getRoute('bogus');
        } catch (Zend_Controller_Router_Exception $e) {
            return true;
        }

        $this->fail();
    }

    public function testAddRoutes()
    {
        $this->_router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $routes = $this->_router->getRoutes();

        $this->assertSame(2, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['archive']);

        $this->_router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));
        $routes = $this->_router->getRoutes();

        $this->assertSame(3, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['register']);
    }

    public function testRoute()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request();
        
        $token = $this->_router->route($request);

        $this->assertType('Zend_Controller_Request_Http', $token);
    }

    public function testEmptyRoute()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/');
        
        $this->_router->removeRoute('default');
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
    
    public function testSetConfig()
    {
        require_once 'Zend/Config/Ini.php';
        $file = dirname(__FILE__) . '/_files/routes.ini';
        $config = new Zend_Config_Ini($file, 'testing');
        
        $this->_router->addConfig($config, 'routes');
        
        $this->assertType('Zend_Controller_Router_StaticRoute', $this->_router->getRoute('news'));
        $this->assertType('Zend_Controller_Router_Route', $this->_router->getRoute('archive'));
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
