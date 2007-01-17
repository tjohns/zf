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

    public function disableTestFormatControllerName()
    {
        $this->assertEquals('IndexController', $this->_dispatcher->formatControllerName('index'));
        $this->assertEquals('Site_CustomController', $this->_dispatcher->formatControllerName('site_custom'));
    }

    public function disableTestFormatActionName()
    {
        $this->assertEquals('indexAction', $this->_dispatcher->formatActionName('index'));
        $this->assertEquals('myindexAction', $this->_dispatcher->formatActionName('myIndex'));
        $this->assertEquals('my_IndexAction', $this->_dispatcher->formatActionName('my_index'));
    }

    public function disableTestGetSetControllerDirectory()
    {
        $test = $this->_dispatcher->getControllerDirectory();
        $this->assertTrue(is_array($test));
        $this->assertEquals(1, count($test));
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files', $test[0]);
    }

    public function disableTestIsDispatchable()
    {
        $request = new Zend_Controller_Request_Http();

        $request->setControllerName('index');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('foo');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('bogus');
        $this->assertFalse($this->_dispatcher->isDispatchable($request));
    }

    public function disableTestSetGetResponse()
    {
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->setResponse($response);
        $this->assertTrue($response === $this->_dispatcher->getResponse());
    }

    public function disableTestSetGetDefaultController()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultController());

        $this->_dispatcher->setDefaultController('foo');
        $this->assertEquals('foo', $this->_dispatcher->getDefaultController());
    }

    public function disableTestSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultAction());

        $this->_dispatcher->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_dispatcher->getDefaultAction());
    }

    /**
     * Test default action on valid controller
     */
    public function disableTestDispatch()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    /**
     * Test valid action on valid controller
     */
    public function disableTestDispatch1()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    /**
     * Test invalid action on valid controller
     */
    public function disableTestDispatch2()
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

    /**
     * Test invalid controller
     */
    public function disableTestDispatch3()
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

    /**
     * Test valid action on valid controller; test pre/postDispatch
     */
    public function disableTestDispatch4()
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

    /**
     * Test defaults of controller and action pair 
     */
    public function disableTestDispatch5()
    {
        $request = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertSame('index', $request->getControllerName());
        $this->assertSame('index', $request->getActionName());
    }

    /**
     * Test that classes are found in modules
     */
    public function disableTestModules()
    {
        $this->_dispatcher->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files',
            'admin'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files/Admin',
        ));

        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('baz');
        $request->setActionName('bar');
        $request->setParam('module', 'admin');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files/Admin', $this->_dispatcher->getDispatchDirectory());

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin's Baz::bar action called", $body, $body);
    }

    /**
     * Test that classes are found in modules, using a prefix
     */
    public function testNamespacedModules()
    {
        $this->_dispatcher->setControllerDirectory(array(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files',
        ));

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo');
        $request->setActionName('bar');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_Foo::bar action called", $body, $body);
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

    public function testNamespacedControllerWithCamelCaseAction()
    {
        $this->_dispatcher->setControllerDirectory(array(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files',
        ));

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo-bar');
        $request->setActionName('baz.bat');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_FooBar::bazBat action called", $body, $body);
    }
}
