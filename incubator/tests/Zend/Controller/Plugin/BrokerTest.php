<?php
require_once 'Zend/Controller/Front.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Controller/Router.php';

class Zend_Controller_Plugin_BrokerTest extends PHPUnit_Framework_TestCase
{
    public function testDuplicatePlugin()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        try {
            $broker->registerPlugin($plugin);
            $this->fail('Duplicate registry of plugin object should be disallowed');
        } catch (Exception $expected) {
            $this->assertContains('already', $expected->getMessage());
        }
    }


    public function testUsingFrontController()
    {
        $controller = new Zend_Controller_Front();
        $controller->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com');
        $controller->setResponse(new Zend_Controller_Response_Cli());
        $controller->setRouter(new Zend_Controller_Router());
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $controller->registerPlugin($plugin);
        $response = $controller->dispatch($request);
        $this->assertTrue($plugin->getObj()->body === '123456');
    }

}

class Zend_Controller_Plugin_BrokerTest_TestPlugin extends Zend_Controller_Plugin_Abstract
{
    protected $_obj;

    public function __construct()
    {
        $this->_obj = new stdClass();
    }

    public function getObj()
    {
        return $this->_obj;
    }

    public function routeStartup()
    {
        $this->_obj->body .= '1';
    }

    public function routeShutdown($request)
    {
        $this->_obj->body .= '2';
    }

    public function dispatchLoopStartup($request)
    {
        $this->_obj->body .= '3';
    }

    public function preDispatch($request)
    {
        $this->_obj->body .= '4';
    }

    public function postDispatch($request)
    {
        $this->_obj->body .= '5';
    }

    public function dispatchLoopShutdown()
    {
        $this->_obj->body .= '6';
    }
}