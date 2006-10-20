<?php
require_once 'Zend/Controller/Router.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';

/**
 * Zend_Controller_RouterTest_Request - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_RouterTest_Request extends Zend_Controller_Request_Http
{
    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://framework.zend.com/foo/bar/baz/2';
        }

        parent::__construct($uri);
    }
}

class Zend_Controller_RouterTest extends PHPUnit_Framework_TestCase 
{
    /**
     * testRoute 
     * 
     * @todo More complex cases, where there are controllers attached to the 
     * router
     */
    public function testRoute()
    {
        $request = new Zend_Controller_RouterTest_Request();
        $router = new Zend_Controller_Router();
        $route = $router->route($request);

        $this->assertEquals('index', $route->getControllerName());
        $this->assertEquals('noRoute', $route->getActionName());

        $request->setControllerName('undefined');
        $request->setActionName('foobar');

        $this->assertEquals('undefined', $route->getControllerName());
        $this->assertEquals('foobar', $route->getActionName());
    }
}
