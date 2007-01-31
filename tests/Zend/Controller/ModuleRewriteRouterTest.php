<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_RewriteRouter */
require_once 'Zend/Controller/ModuleRewriteRouter.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'PHPUnit/Runner/Version.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_ModuleRewriteRouterTest extends PHPUnit_Framework_TestCase
{
    protected $_router;
    
    public function setUp() 
    {
        $this->_router = new Zend_Controller_ModuleRewriteRouter();
        $this->_router->setFrontController(new Zend_Controller_ModuleRewriteRouterTest_FrontController());
    }
    
    public function tearDown() 
    {
        unset($this->_router);
    }

    public function testDefaultRouteMatchedWithModules()
    {
        $request = new Zend_Controller_ModuleRewriteRouterTest_Request('http://localhost/mod/ctrl/act');
        $token = $this->_router->route($request);
        
        $this->assertSame('mod', $token->getParam('module')); // getModuleName does not exist yet
        $this->assertSame('ctrl', $token->getControllerName());
        $this->assertSame('act', $token->getActionName());
    }

    public function testRouteCompatDefaults()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/');
        
        $token = $this->_router->route($request);

        $this->assertSame('default', $token->getParam('module'));
        $this->assertSame('defctrl', $token->getControllerName());
        $this->assertSame('defact', $token->getActionName());
    }
    
    public function testDefaultRouteWithEmptyControllerAndAction()
    {
        $request = new Zend_Controller_RewriteRouterTest_Request('http://localhost/mod');
        
        $token = $this->_router->route($request);

        $this->assertSame('mod', $token->getParam('module'));
        $this->assertSame('defctrl', $token->getControllerName());
        $this->assertSame('defact', $token->getActionName());
    }

}

/**
 * Zend_Controller_RouterTest_Dispatcher
 */
class Zend_Controller_ModuleRewriteRouterTest_Dispatcher 
{
    public function getDefaultController() {
        return 'defctrl';
    }
    public function getDefaultAction() {
        return 'defact';
    }
}

/**
 * Zend_Controller_ModuleRewriteRouterTest_FrontController
 * 
 * $router->setFrontController() doesn't use an interface, so unfortunately the
 * base class has to be extended
 */
class Zend_Controller_ModuleRewriteRouterTest_FrontController extends Zend_Controller_Front 
{
    protected $_dispatcher;
    
    public function __construct() 
    {
        $this->_dispatcher = new Zend_Controller_ModuleRewriteRouterTest_Dispatcher();
    }
    public function getDispatcher() 
    {
        return $this->_dispatcher;
    }
}

/**
 * Zend_Controller_ModuleRewriteRouterTest_Request - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_ModuleRewriteRouterTest_Request extends Zend_Controller_Request_Http
{
    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://localhost/foo/bar/baz/2';
        }

        parent::__construct($uri);
    }
}

