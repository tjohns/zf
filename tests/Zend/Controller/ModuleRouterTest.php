<?php
require_once 'Zend/Controller/ModuleRouter.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';

class Zend_Controller_ModuleRouterTest extends PHPUnit_Framework_TestCase 
{
    public function setUp()
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
    }

    /**
     * testRoute 
     */
    public function testRoute()
    {
        $this->front->setControllerDirectory(array(
            'foo' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files'
        ));
        $request = new Zend_Controller_ModuleRouterTest_Request();
        $router  = new Zend_Controller_ModuleRouter();
        $router->setFrontController($this->front);
        $route   = $router->route($request);

        $this->assertEquals('foo', $request->getModuleName(), $request->getPathInfo());
        $this->assertEquals('bar', $request->getControllerName(), $request->getPathInfo());
        $this->assertEquals('baz', $request->getActionName(), $request->getPathInfo());
        $params = $route->getParams();
        $this->assertTrue(isset($params['val']), $request->getPathInfo());
        $this->assertEquals(2, $params['val'], $request->getPathInfo());
    }
}

/**
 * Zend_Controller_ModuleRouterTest_Request - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_ModuleRouterTest_Request extends Zend_Controller_Request_Http
{
    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://framework.zend.com/foo/bar/baz/val/2';
        }

        parent::__construct($uri);
    }
}


