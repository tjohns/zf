<?php
require_once 'Zend/Controller/ModuleDispatcher.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

class Zend_Controller_ModuleDispatcherTest extends PHPUnit_Framework_TestCase 
{
    protected $_dispatcher;

    public function setUp()
    {
        $this->_dispatcher = new Zend_Controller_ModuleDispatcher();
        $this->_dispatcher->setControllerDirectory(array(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files',
        ));
        $this->_dispatcher->addControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin', 'admin');
    }

    public function tearDown()
    {
        unset($this->_dispatcher);
    }

    public function testSetGetControllerDirectory()
    {
        $expected = array(
            'default' => array(
                dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files'
            ),
            'admin'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin'
        );
        $dirs = $this->_dispatcher->getControllerDirectory();
        $this->assertEquals($expected, $dirs);
    }

    public function testIsDispatchable()
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
        $this->_dispatcher->setDefaultController('foo')
             ->setParam('useDefaultControllerAlways', true);

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('admin');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_Foo::index action called", $body, $body);
    }

    public function testUseGlobalDefaultController()
    {
        $this->_dispatcher->setParam('useGlobalDefault', true)
             ->setParam('useDefaultControllerAlways', true);

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('admin');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Index action called", $body, $body);
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

    public function testSettingDefaultControllerDirectoryAsArray()
    {
        $this->_dispatcher->setControllerDirectory(array(
                 'default' => array(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files')))
             ->addControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin', 'admin')
             ->setParam('useGlobalDefault', true);
        $controllerDirs = $this->_dispatcher->getControllerDirectory();
        $this->assertTrue(isset($controllerDirs['default']));
        $this->assertTrue(is_array($controllerDirs['default']));
        $this->assertTrue(is_string($controllerDirs['default'][0]));
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files', $controllerDirs['default'][0]);
    }

    public function testSettingDefaultControllerDirectoryAsArrayFromFrontController()
    {
        require_once 'Zend/Controller/Front.php';
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setControllerDirectory(array(
            'default' => array(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files')
        ));
        $this->_dispatcher->setFrontController($front);

        $controllerDirs = $this->_dispatcher->getControllerDirectory();
        $this->assertTrue(isset($controllerDirs['default']));
        $this->assertTrue(is_array($controllerDirs['default']));
        $this->assertTrue(is_string($controllerDirs['default'][0]));
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files', $controllerDirs['default'][0]);
    }
}
