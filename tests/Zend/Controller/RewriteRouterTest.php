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
class Zend_Controller_RewriteRouterTest extends PHPUnit_Framework_TestCase
{

    protected $router;
    protected $dispatcher;

    public function setUp()
    {
        error_reporting(E_ALL | E_STRICT);
        $_SERVER['REQUEST_URI'] = '';
        $this->router = new Zend_Controller_RewriteRouter();
        $this->router->setRewriteBase('/');
        $this->dispatcher = new Zend_Controller_Dispacher_Mock();
        
        $this->version = (version_compare(PHPUnit_Runner_Version::id(), '3.0.0alpha11') >= 0) ? 3 : 2;
    }

    public function testDefaultRoute()
    {
        $routes = $this->router->getRoutes();
        $this->assertType('Zend_Controller_Router_Route', $routes['default']);
    }

    public function testGetRoute()
    {
        $route = $this->router->getRoute('default');
        $routes = $this->router->getRoutes();
    
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
        $this->router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $routes = $this->router->getRoutes();

        $this->assertSame(3, count($routes));
        $this->assertType('Zend_Controller_Router_Route', $routes['archive']);

        $this->router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));
        $routes = $this->router->getRoutes();

        $this->assertSame(4, count($routes));
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

        $this->assertSame('index', $token->getControllerName());
        $this->assertSame('index', $token->getActionName());
    }

    public function testRouteNotMatched()
    {
        $this->router->addRoute('compat', new Zend_Controller_Router_Route(':controller/:action'));
        
        $_SERVER['REQUEST_URI'] = 'archive/action/bogus';
        $token = $this->router->route($this->dispatcher);

        $this->assertSame('index', $token->getControllerName());
        $this->assertSame('noRoute', $token->getActionName());
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
        $this->router->addRoute('test', new Zend_Controller_Router_Route('test/:type/:something', array('controller' => 'test', 'action' => 'act')));

        $_SERVER['REQUEST_URI'] = 'test/abc%34as%20df/def';
        $token = $this->router->route($this->dispatcher);
        
        $this->assertSame('act', $token->getActionName());
        $this->assertSame('test', $token->getControllerName());

        $params = $token->getParams();

        $this->assertSame('abc4as df', $params['type']);
        $this->assertSame('def', $params['something']);
    }

    public function testDefaultRouteMatched()
    {
        $this->router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $this->router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $_SERVER['REQUEST_URI'] = 'ctrl/act';
        $token = $this->router->route($this->dispatcher);

        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }


    public function testFirstRouteMatched()
    {
        $this->router->addRoute('archive', new Zend_Controller_Router_Route('archive/:year', array('year' => '2006', 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+')));
        $this->router->addRoute('register', new Zend_Controller_Router_Route('register/:action', array('controller' => 'profile', 'action' => 'register')));

        $_SERVER['REQUEST_URI'] = 'archive/2006';
        $token = $this->router->route($this->dispatcher);

        $this->assertSame('archive', $token->getControllerName());
        $this->assertSame('show', $token->getActionName());
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
        // RewriteRule under Apache <VirtualHost>
        // http://bugs.php.net/bug.php?id=38141
        // http://issues.apache.org/bugzilla/show_bug.cgi?id=40102
        
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs/test';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '';
        $_SERVER['PATH_INFO'] = '/index.html'; // #@$%#! This bug is awful! 

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('', $rwBase);
    }

    public function testRewriteBaseDeepUrlWithApacheConfigRewrite()
    {
        // RewriteRule under Apache <VirtualHost>
        // http://bugs.php.net/bug.php?id=38141
        // http://issues.apache.org/bugzilla/show_bug.cgi?id=40102

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs/test';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/archive/2006/05';
        $_SERVER['SCRIPT_NAME'] = '';
        $_SERVER['PATH_INFO'] = '/archive/2006/05';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('', $rwBase);
    }

    public function testRewriteBaseAbsoluteRootWithRewrite()
    {
        // RewriteRule in .htaccess 
        // Absolute vhost root 

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs/test';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PATH_INFO'] = null;

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('', $rwBase);
    }

    public function testRewriteBaseAbsoluteRootWithoutRewrite()
    {
        // RewriteRule in .htaccess
        // Absolute vhost root 

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs/test';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PATH_INFO'] = null;

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('', $rwBase);
    }

    public function testRewriteBaseAbsoluteRootWithoutRewrite2()
    {
        // RewriteRule in .htaccess
        // Absolute vhost root 

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs/test';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PATH_INFO'] = null;

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/index.php', $rwBase);
    }

    public function testRewriteBaseAbsoluteRootWithoutRewriteAndDeepUrl()
    {
        // RewriteRule in .htaccess 
        // Absolute vhost root 

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs/test';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php/archive/2006/05';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PATH_INFO'] = '/archive/2006/05';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/index.php', $rwBase);
    }

    public function testRewriteBaseRootWithRewrite()
    {
        // RewriteRule in .htaccess 
        // subdir root 

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/test/';
        $_SERVER['SCRIPT_NAME'] = '/test/index.php';
        $_SERVER['PATH_INFO'] = null;

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/test', $rwBase);
    }

    public function testRewriteBaseRootWithoutRewrite()
    {
        // RewriteRule in .htaccess 
        // subdir root 

        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/test/index.php';
        $_SERVER['SCRIPT_NAME'] = '/test/index.php';
        $_SERVER['PATH_INFO'] = null;

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/test/index.php', $rwBase);
    }

    public function testRewriteBaseDeepUrlWithRewrite()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/test/archive/2006/05';
        $_SERVER['SCRIPT_NAME'] = '/test/index.php';
        $_SERVER['PATH_INFO'] = '/archive/2006/05';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/test', $rwBase);
    }

    public function testRewriteBaseDeepUrlWithoutRewrite()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/test/index.php/archive/2006/05';
        $_SERVER['SCRIPT_NAME'] = '/test/index.php';
        $_SERVER['PATH_INFO'] = '/archive/2006/05';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/test/index.php', $rwBase);
    }

    public function testRewriteBaseDeepUrlWithRewriteAndFakeIndex()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/test/archive/2006/05/index.php';
        $_SERVER['SCRIPT_NAME'] = '/test/index.php';
        $_SERVER['PATH_INFO'] = '/archive/2006/05/index.php';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('/test', $rwBase);
    }

    public function testRewriteBaseWithFakeIndexAtRoot()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/localhost/htdocs';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/localhost/htdocs/test/index.php';
        $_SERVER['REQUEST_URI'] = '/archive/2006/05/index.php';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PATH_INFO'] = '/archive/2006/05/index.php';

        // Redundant to setUp
        $router = new Zend_Controller_RewriteRouter();
        $rwBase = $router->detectRewriteBase();

        $this->assertSame('', $rwBase);
    }

}
