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

