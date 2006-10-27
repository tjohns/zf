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

    public function testDefaultRoute()
    {
        $router = new Zend_Controller_RewriteRouter();
        $routes = $router->getRoutes();
        $this->assertType('Zend_Controller_Router_Route', $routes['default']);
    }

    public function testGetRoute()
    {
        $router = new Zend_Controller_RewriteRouter();
        $route = $router->getRoute('default');
        $routes = $router->getRoutes();
    
        $this->assertType('Zend_Controller_Router_Route', $route);
        $this->assertSame($route, $routes['default']);
    }

    public function testGetNonExistentRoute()
    {
        $router = new Zend_Controller_RewriteRouter();
        
        try {
            $route = $router->getRoute('bogus');
        } catch (Zend_Controller_Router_Exception $e) {
            return true;
        }

        $this->fail();
    }

    public function testAddRoutes()
    {
        $router = new Zend_Controller_RewriteRouter();

        $router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $routes = $router->getRoutes();

        $this->assertSame(3, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['archive']);

        $router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));
        $routes = $router->getRoutes();

        $this->assertSame(4, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['register']);
    }

    public function testRoute()
    {
        $router = new Zend_Controller_RewriteRouter();
        $request = new Zend_Controller_RewriteRouterTest_Request();
        
        $token = $router->route($request);

        $this->assertType('Zend_Controller_Request_Http', $token);
    }

    public function testRouteDefault()
    {
        $router = new Zend_Controller_RewriteRouter();
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/');

        $token = $router->route($request);

        $this->assertSame('index', $token->getControllerName());
        $this->assertSame('index', $token->getActionName());
    }

    public function testRouteNotMatched()
    {
        $this->markTestSkipped('Needs a rewrite for new Controller structure');
        
        $router = new Zend_Controller_RewriteRouter();
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/archive/action/bogus');

        $router->addRoute('compat', new Zend_Controller_Router_Route(':controller/:action'));
        
        $token = $router->route($request);

        $this->assertSame('index', $token->getControllerName());
        $this->assertSame('noRoute', $token->getActionName());
    }

    public function testDefaultRouteMatched()
    {
        $router = new Zend_Controller_RewriteRouter();
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/ctrl/act');

        $router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $token = $router->route($request);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }


    public function testFirstRouteMatched()
    {
        $router = new Zend_Controller_RewriteRouter();
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/archive/2006');

        $router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $token = $router->route($request);

        $this->assertSame('archive', $token->getControllerName());
        $this->assertSame('show', $token->getActionName());
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
