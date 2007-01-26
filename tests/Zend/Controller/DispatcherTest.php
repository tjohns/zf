<?php
require_once 'Zend/Controller/Dispatcher.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

class Zend_Controller_DispatcherTest extends PHPUnit_Framework_TestCase 
{
    protected $_dispatcher;

    public function setUp()
    {
        $this->_dispatcher = new Zend_Controller_Dispatcher();
        $this->_dispatcher->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
    }

    public function tearDown()
    {
        unset($this->_dispatcher);
    }

    public function testFormatControllerName()
    {
        $this->assertEquals('IndexController', $this->_dispatcher->formatControllerName('index'));
        $this->assertEquals('Site_CustomController', $this->_dispatcher->formatControllerName('site_custom'));
    }

    public function testFormatActionName()
    {
        $this->assertEquals('indexAction', $this->_dispatcher->formatActionName('index'));
        $this->assertEquals('myindexAction', $this->_dispatcher->formatActionName('myIndex'));
        $this->assertEquals('myindexAction', $this->_dispatcher->formatActionName('my_index'));
        $this->assertEquals('myIndexAction', $this->_dispatcher->formatActionName('my.index'));
        $this->assertEquals('myIndexAction', $this->_dispatcher->formatActionName('my-index'));
    }

    public function testGetSetControllerDirectory()
    {
        $test = $this->_dispatcher->getControllerDirectory();
        $this->assertTrue(is_array($test));
        $this->assertEquals(1, count($test));
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files', $test[0]);
    }

    public function testIsDispatchable()
    {
        $request = new Zend_Controller_Request_Http();

        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('index');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('foo');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        // True, because it will dispatch to default controller
        $request->setControllerName('bogus');
        $this->assertFalse($this->_dispatcher->isDispatchable($request));
    }

    public function testSetGetResponse()
    {
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->setResponse($response);
        $this->assertTrue($response === $this->_dispatcher->getResponse());
    }

    public function testSetGetDefaultController()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultController());

        $this->_dispatcher->setDefaultController('foo');
        $this->assertEquals('foo', $this->_dispatcher->getDefaultController());
    }

    public function testSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultAction());

        $this->_dispatcher->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_dispatcher->getDefaultAction());
    }

    public function testDispatchValidControllerDefaultAction()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    public function testDispatchValidControllerAndAction()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    public function testDispatchValidControllerWithInvalidAction()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('foo');
        $response = new Zend_Controller_Response_Cli();

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should be raised by __call');
        } catch (Exception $e) {
            // success
        }
    }

    public function testDispatchInvalidController()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('bogus');
        $response = new Zend_Controller_Response_Cli();

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should be raised; no such controller');
        } catch (Exception $e) {
            // success
        }
    }

    public function testDispatchInvalidControllerUsingDefaults()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('bogus');
        $response = new Zend_Controller_Response_Cli();

        $this->_dispatcher->setParam('useDefaultControllerAlways', true);

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->assertSame('index', $request->getControllerName());
            $this->assertSame('index', $request->getActionName());
        } catch (Exception $e) {
            $this->fail('Exception should not be raised when useDefaultControllerAlways set');
        }
    }

    public function testDispatchValidControllerWithPrePostDispatch()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('foo');
        $request->setActionName('bar');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains('Bar action called', $body);
        $this->assertContains('preDispatch called', $body);
        $this->assertContains('postDispatch called', $body);
    }

    public function testDispatchNoControllerUsesDefaults()
    {
        $request = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertSame('index', $request->getControllerName());
        $this->assertSame('index', $request->getActionName());
    }

    /**
     * Tests ZF-637 -- action names with underscores not being correctly changed to camelCase
     */
    public function testZf637()
    {
        $test = $this->_dispatcher->formatActionName('view_entry');
        $this->assertEquals('viewentryAction', $test);
    }

    public function testWordDelimiter()
    {
        $this->assertEquals(array('-', '.'), $this->_dispatcher->getWordDelimiter());
        $this->_dispatcher->setWordDelimiter(':');
        $this->assertEquals(array(':'), $this->_dispatcher->getWordDelimiter());
    }

    public function testPathDelimiter()
    {
        $this->assertEquals('_', $this->_dispatcher->getPathDelimiter());
        $this->_dispatcher->setPathDelimiter(':');
        $this->assertEquals(':', $this->_dispatcher->getPathDelimiter());
    }
}
