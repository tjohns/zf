<?php
require_once 'Zend/Controller/Action.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

class Zend_Controller_ActionTest extends PHPUnit_Framework_TestCase 
{
    public function setUp()
    {
        $this->_controller = new Zend_Controller_ActionTest_TestController(
            new Zend_Controller_Request_Http(),
            new Zend_Controller_Response_Cli(),
            'foo',
            'bar'
        );
    }

    public function tearDown()
    {
        unset($this->_controller);
    }

    public function testInit()
    {
        $this->assertEquals('foo', $this->_controller->initArgs['foo']);
        $this->assertEquals('bar', $this->_controller->initArgs['bar']);
    }

    public function testPreRun()
    {
        $this->_controller->preDispatch();
        $this->assertNotContains('Prerun ran', $this->_controller->getResponse()->getBody());

        $this->_controller->getRequest()->setParam('prerun', true);
        $this->_controller->preDispatch();
        $this->assertContains('Prerun ran', $this->_controller->getResponse()->getBody());
    }

    public function testPostRun()
    {
        $this->_controller->postDispatch();
        $this->assertNotContains('Postrun ran', $this->_controller->getResponse()->getBody());

        $this->_controller->getRequest()->setParam('postrun', true);
        $this->_controller->postDispatch();
        $this->assertContains('Postrun ran', $this->_controller->getResponse()->getBody());
    }

    public function testGetRequest()
    {
        $this->assertTrue($this->_controller->getRequest() instanceof Zend_Controller_Request_Abstract);
    }

    public function testGetResponse()
    {
        $this->assertTrue($this->_controller->getResponse() instanceof Zend_Controller_Response_Abstract);
    }

    public function testGetInvokeArgs()
    {
        $expected = array('foo', 'bar');
        $this->assertSame($expected, $this->_controller->getInvokeArgs());
    }

    public function testForward()
    {
        $this->_controller->forward();
        $this->assertEquals('baz', $this->_controller->getRequest()->getControllerName());
        $this->assertEquals('forwarded', $this->_controller->getRequest()->getActionName());
    }

    public function testRun()
    {
        $response = $this->_controller->run();
        $this->assertContains('In the index action', $response->getBody());
        $this->assertNotContains('Prerun ran', $this->_controller->getResponse()->getBody());
    }

    public function testRun2()
    {
        $this->_controller->getRequest()->setActionName('bar');
        try {
            $response = $this->_controller->run();
            $this->fail('Should not be able to call bar as action');
        } catch (Exception $e) {
            //success!
        } 
    }

    public function testRun3()
    {
        $this->_controller->getRequest()->setActionName('foo');
        $response = $this->_controller->run();
        $this->assertContains('In the foo action', $response->getBody());
        $this->assertNotContains('Prerun ran', $this->_controller->getResponse()->getBody());
    }
}

class Zend_Controller_ActionTest_TestController extends Zend_Controller_Action
{
    public $initArgs = array();

    public function init($var1, $var2)
    {
        $this->initArgs['foo'] = $var1;
        $this->initArgs['bar'] = $var2;
    }

    public function preDispatch()
    {
        if (false !== ($param = $this->_getParam('prerun', false))) {
            $this->getResponse()->appendBody("Prerun ran\n");
        }
    }

    public function postDispatch()
    {
        if (false !== ($param = $this->_getParam('postrun', false))) {
            $this->getResponse()->appendBody("Postrun ran\n");
        }
    }

    public function noRouteAction()
    {
        return $this->indexAction();
    }

    public function indexAction()
    {
        $this->getResponse()->appendBody("In the index action\n");
    }

    public function fooAction()
    {
        $this->getResponse()->appendBody("In the foo action\n");
    }

    public function bar()
    {
        $this->getResponse()->setBody("Should never see this\n");
    }

    public function forward()
    {
        $this->_forward('baz', 'forwarded');
    }
}
