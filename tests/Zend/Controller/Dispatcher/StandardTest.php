<?php
require_once 'Zend/Controller/Dispatcher/Standard.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

class Zend_Controller_Dispatcher_StandardTest extends PHPUnit_Framework_TestCase 
{
    protected $_dispatcher;

    public function setUp()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'admin'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin'
        ));
        $this->_dispatcher = new Zend_Controller_Dispatcher_Standard();
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

    public function testSetGetControllerDirectory()
    {
        $expected = array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'admin'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin'
        );
        $dirs = $this->_dispatcher->getControllerDirectory();
        $this->assertEquals($expected, $dirs);
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

    public function testModuleIsDispatchable()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo');
        $request->setActionName('bar');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $request->setModuleName('bogus');
        $request->setControllerName('bogus');
        $request->setActionName('bar');
        $this->assertFalse($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));
    }

    public function testSetGetResponse()
    {
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->setResponse($response);
        $this->assertTrue($response === $this->_dispatcher->getResponse());
    }

    public function testSetGetDefaultControllerName()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultControllerName());

        $this->_dispatcher->setDefaultControllerName('foo');
        $this->assertEquals('foo', $this->_dispatcher->getDefaultControllerName());
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

    /**
     * Test that classes are found in modules, using a prefix
     */
    public function testModules()
    {
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

    public function testModuleControllerInSubdirWithCamelCaseAction()
    {
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

    public function testUseModuleDefaultController()
    {
        $this->_dispatcher->setDefaultControllerName('foo')
             ->setParam('useDefaultControllerAlways', true);

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('admin');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_Foo::index action called", $body, $body);
    }

    public function testNoModuleOrControllerDefaultsCorrectly()
    {
        $request = new Zend_Controller_Request_Http('http://example.com/');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Index action called", $body, $body);
    }

    public function testOutputBuffering()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('ob');
        $request->setActionName('index');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("OB index action called", $body, $body);
    }

    public function testDisableOutputBuffering()
    {
        if (!defined('TESTS_ZEND_CONTROLLER_DISPATCHER_OB') || !TESTS_ZEND_CONTROLLER_DISPATCHER_OB) {
            $this->markTestSkipped('Skipping output buffer disabling in Zend_Controller_Dispatcher_Standard');
        }

        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('ob');
        $request->setActionName('index');
        $this->_dispatcher->setParam('disableOutputBuffering', true);

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertEquals('', $body, $body);
    }

    public function testModuleSubdirControllerFound()
    {
        Zend_Controller_Front::getInstance()->addControllerDirectory(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'controllers',
            'foo'
        );

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('foo');
        $request->setControllerName('admin_index');
        $request->setActionName('index');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Foo_Admin_IndexController::indexAction() called", $body, $body);
    }
}
