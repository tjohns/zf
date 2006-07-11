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

/** PHPUnit2 test case */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * Mock Dispatcher
 */
class Zend_Controller_Dispacher_Mock implements Zend_Controller_Dispatcher_Interface
{

    public $dispatchable = true;

    public function formatControllerName($unformatted)
    {
        return $unformatted;
    }

    public function formatActionName($unformatted)
    {
        return $unformatted;
    }

    public function isDispatchable(Zend_Controller_Dispatcher_Token $route)
    {
        return $this->dispatchable;
    }

    public function dispatch(Zend_Controller_Dispatcher_Token $route) {}
}

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_RewriteRouterTest extends PHPUnit2_Framework_TestCase
{

    protected $router;
    protected $dispatcher;

    public function setUp()
    {
        error_reporting(E_ALL | E_STRICT);
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_FILENAME'] = '/home/martel/WWW/test/index.php';
        $this->router = new Zend_Controller_RewriteRouter();
        $this->dispatcher = new Zend_Controller_Dispacher_Mock();
    }

    public function testDefaultRoute()
    {
        $routes = $this->getNonPublicProperty($this->router, '_routes');
        $this->assertType('Zend_Controller_Router_Route', $routes['default']);
    }

    public function testGetRoute()
    {
        $route = $this->router->getRoute('default');
        $routes = $this->getNonPublicProperty($this->router, '_routes');

        $this->assertType('Zend_Controller_Router_Route', $route);
        $this->assertSame($route, $routes['default']);
    }

    public function testGetNonExistentRoute()
    {
        try {
            $route = $this->router->getRoute('bogus');
        } catch (Exception $e) {
            return true;
        }

        $this->fail();

    }

    public function testAddRoutes()
    {
        $this->router->addRoute('archive', 'archive/:year', array('year' => 2006, 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $routes = $this->getNonPublicProperty($this->router, '_routes');

        $this->assertEquals(3, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['archive']);

        $this->router->addRoute('register', 'register/:action', array('controller' => 'profile', 'action' => 'register'));
        $routes = $this->getNonPublicProperty($this->router, '_routes');

        $this->assertEquals(4, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['register']);

    }

    public function testRoute()
    {
        $token = $this->router->route($this->dispatcher);
        $this->assertType('Zend_Controller_Dispatcher_Token', $token);
    }

    public function testRouteDefault()
    {
        $_SERVER['REQUEST_URI'] = '';
        $token = $this->router->route($this->dispatcher);

        $this->assertEquals('index', $token->getControllerName());
        $this->assertEquals('index', $token->getActionName());
    }

    public function testRouteNotMatched()
    {
        $_SERVER['REQUEST_URI'] = 'archive/action/bogus';
        $token = $this->router->route($this->dispatcher);

        $this->assertEquals('index', $token->getControllerName());
        $this->assertEquals('noRoute', $token->getActionName());
    }

    /*
        Kevin McArthur:

        I think i've sourced another small bug in the RewriteRouter.

        http://url.com/test/abc%34asdf/def

        $router->addRoute('test/:type/:something', array('controller' => 'test',
        'action' => 'index'));

        This causes the router to try to noroute action (when theres a urlencoded
        value in the url for a parameter)

        In fact the url should appear as

        http://url.com/test/abc4asdf/def

        to the router, and the value should properly end up in the type parameter as
        'abc4asdf' as it does if its not encoded.
    */
    public function testRouteWithEncodedUrl()
    {

        $this->markTestSkipped('To be resolved with Zend_Http_Request');

        $this->router->addRoute('test', 'test/:type/:something', array('controller' => 'test', 'action' => 'act'));

        $_SERVER['REQUEST_URI'] = 'test/abc%34asdf/def';
        $token = $this->router->route($this->dispatcher);

        $this->assertEquals('act', $token->getActionName());
        $this->assertEquals('test', $token->getControllerName());

        $params = $token->getParams();

        $this->assertEquals('abc4asdf', $params['type']);
        $this->assertEquals('def', $params['something']);

    }

    public function testDefaulRouteMatched()
    {

        $this->router->addRoute('archive', 'archive/:year', array('year' => 2006, 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $this->router->addRoute('register', 'register/:action', array('controller' => 'profile', 'action' => 'register'));

        $_SERVER['REQUEST_URI'] = 'ctrl/act';
        $token = $this->router->route($this->dispatcher);

        $this->assertEquals('ctrl', $token->getControllerName());
        $this->assertEquals('act', $token->getActionName());

    }


    public function testFirstRouteMatched()
    {

        $this->router->addRoute('archive', 'archive/:year', array('year' => 2006, 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $this->router->addRoute('register', 'register/:action', array('controller' => 'profile', 'action' => 'register'));

        $_SERVER['REQUEST_URI'] = 'archive/2006';
        $token = $this->router->route($this->dispatcher);

        $this->assertEquals('archive', $token->getControllerName());
        $this->assertEquals('show', $token->getActionName());

    }

    public function testNotDispatchable()
    {
        $this->dispatcher->dispatchable = false;

        try {
            $this->router->route($this->dispatcher);
        } catch (Zend_Controller_Router_Exception $e) {
            return true;
        }

        $this->fail('Unroutable object passed');

    }

    public function testRewriteBaseRootWithApacheConfigRewrite()
    {
        $_SERVER['SCRIPT_NAME'] = '/';
        $_SERVER['REQUEST_URI'] = '/';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('', $rwBase);

    }

    public function testRewriteBaseDeepUrlWithApacheConfigRewrite()
    {
        $_SERVER['SCRIPT_NAME'] = '/news/create';
        $_SERVER['REQUEST_URI'] = '/news/create';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('', $rwBase);

    }

    public function testRewriteBaseAbsoluteRootWithRewriteAndEmptyRequestUri()
    {

        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('', $rwBase);

    }

    public function testRewriteBaseAbsoluteRootWithRewrite()
    {

        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('', $rwBase);

    }

    public function testRewriteBaseAbsoluteRootWithoutRewrite()
    {

        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('/index.php', $rwBase);

    }

    public function testRewriteBaseRootWithRewrite()
    {

        $_SERVER['SCRIPT_NAME'] = '/aiev5/www/index.php';
        $_SERVER['REQUEST_URI'] = '/aiev5/www/';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('/aiev5/www', $rwBase);

    }

    public function testRewriteBaseRootWithoutRewrite()
    {

        $_SERVER['SCRIPT_NAME'] = '/aiev5/www/index.php';
        $_SERVER['REQUEST_URI'] = '/aiev5/www/index.php';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('/aiev5/www/index.php', $rwBase);

    }

    public function testRewriteBaseDeepUrlWithRewrite()
    {

        $_SERVER['SCRIPT_NAME'] = '/aiev5/www/index.php';
        $_SERVER['REQUEST_URI'] = '/aiev5/www/publish/article';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('/aiev5/www', $rwBase);

    }

    public function testRewriteBaseDeepUrlWithoutRewrite()
    {

        $_SERVER['SCRIPT_NAME'] = '/aiev5/www/index.php';
        $_SERVER['REQUEST_URI'] = '/aiev5/www/index.php/publish/article';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->getRewriteBase();

        $this->assertEquals('/aiev5/www/index.php', $rwBase);

    }

}
