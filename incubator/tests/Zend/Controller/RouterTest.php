<?php
require_once 'Zend/Controller/Router.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

require_once 'Zend/Controller/Request/Interface.php';

/**
 * Zend_Controller_RouterTest_Request - request object for router testing
 * 
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_RouterTest_Request implements Zend_Controller_Request_Interface
{
    protected $_controllerName = 'index';
    protected $_actionName = 'index';
    protected $_params = array();

    public function getControllerName()
    {
        return $this->_controllerName;
    }
 
    public function setControllerName($value)
    {
        $this->_controllerName = (string) $value;
    }
 
    public function getActionName()
    {
        return $this->_actionName;
    }
 
    public function setActionName($value)
    {
        $this->_actionName = (string) $value;
    }
 
    public function getParam($key)
    {
        return (isset($this->_params[$key]) ? $this->_params[$key] : null);
    }
 
    public function setParam($key, $value)
    {
        $this->_params[(string) $key] = $value;
    }
 
    public function getParams()
    {
        return $this->_params;
    }
 
    public function setParams($array)
    {
        if (!is_array($array) || (array_keys($array) === range(0, count($array) - 1))) {
            throw Zend_Controller_Exception('Invalid array passed to setParams');
        }

        $this->_params = array_merge($this->_params, $array);
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
        $this->assertEquals('index', $route->getActionName());

        $request->setControllerName('undefined');
        $request->setActionName('foobar');

        $this->assertEquals('index', $route->getControllerName());
        $this->assertEquals('index', $route->getActionName());
    }
}
