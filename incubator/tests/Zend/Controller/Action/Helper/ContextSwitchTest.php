<?php
// Call Zend_Controller_Action_Helper_ContextSwitchTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_ContextSwitchTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Action/Helper/ContextSwitch.php';

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Layout.php';

/**
 * Test class for Zend_Controller_Action_Helper_ContextSwitch.
 */
class Zend_Controller_Action_Helper_ContextSwitchTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_ContextSwitchTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Controller_Action_Helper_ContextSwitchTest_LayoutOverride::$_mvcInstance = null;
        Zend_Controller_Action_HelperBroker::resetHelpers();

        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->addModuleDirectory(dirname(__FILE__) . '/../../_files');

        $this->layout = Zend_Layout::startMvc();

        $this->helper = new Zend_Controller_Action_Helper_ContextSwitch();

        $this->request = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Cli();

        $this->front->setRequest($this->request)->setResponse($this->response);

        $this->controller = new Zend_Controller_Action_Helper_ContextSwitchTestController(
            $this->request,
            $this->response,
            array()
        );
        $this->helper->setActionController($this->controller);

        $this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->viewRenderer->initView();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testDirectReturnsObjectInstance()
    {
        $helper = $this->helper->direct();
        $this->assertSame($this->helper, $helper);
    }

    public function testSetSuffixModifiesContextSuffix()
    {
        $this->helper->setSuffix('xml', 'foobar');
        $this->assertEquals('foobar', $this->helper->getSuffix('xml'));
    }

    public function testSuffixAccessorsThrowExceptionOnInvalidContextType()
    {
        try {
            $this->helper->setSuffix('foobar', 'foobar');
            $this->fail('setSuffix() should throw exception with invalid context type');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('Cannot set suffix', $e->getMessage());
        }

        try {
            $this->helper->getSuffix('foobar');
            $this->fail('getSuffix() should throw exception with invalid context type');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('Cannot retrieve suffix', $e->getMessage());
        }
    }

    public function testSetHeaderModifiesContextHeader()
    {
        $this->helper->setHeader('xml', 'X-Custom-Header', 'application/x-xml');
        $header = $this->helper->getHeader('xml');
        $this->assertTrue(is_array($header));
        $this->assertTrue(isset($header['type']));
        $this->assertTrue(isset($header['content']));
        $this->assertEquals('X-Custom-Header', $header['type']);
        $this->assertEquals('application/x-xml', $header['content']);
    }

    public function testSetHeaderWithoutHeaderArgumentDisablesHeader()
    {
        $this->helper->setHeader('xml');
        $header = $this->helper->getHeader('xml');
        $this->assertNull($header);

        $this->helper->setHeader('json', 'Content-Type');
        $header = $this->helper->getHeader('json');
        $this->assertNull($header);
    }

    public function testHeaderAccessorsThrowExceptionOnInvalidContextType()
    {
        try {
            $this->helper->setHeader('foobar', 'foobar', 'baz');
            $this->fail('setHeader() should throw exception with invalid context type');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('Cannot set header', $e->getMessage());
        }

        try {
            $this->helper->getHeader('foobar');
            $this->fail('getHeader() should throw exception with invalid context type');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('Cannot retrieve header', $e->getMessage());
        }
    }

    public function testDefaultContextParam()
    {
        $this->assertEquals('format', $this->helper->getContextParam());
    }

    public function testCanSetContextParam()
    {
        $this->helper->setContextParam('foobar');
        $this->assertEquals('foobar', $this->helper->getContextParam());
    }

    public function testDefaultContext()
    {
        $this->assertEquals('xml', $this->helper->getDefaultContext());
    }

    public function testCanSetDefaultContext()
    {
        $this->helper->setDefaultContext('json');
        $this->assertEquals('json', $this->helper->getDefaultContext());
    }

    public function testSetDefaultContextThrowsExceptionIfContextDoesNotExist()
    {
        try {
            $this->helper->setDefaultContext('foobar');
            $this->fail('setDefaultContext() should raise exception if context does not exist');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('Cannot set default context', $e->getMessage());
        }
    }

    public function testContextSwitchDisablesLayoutsByDefault()
    {
        $this->assertTrue($this->helper->getAutoDisableLayout());
    }

    public function testCanChooseWhetherLayoutsAreDisabled()
    {
        $this->helper->setAutoDisableLayout(false);
        $this->assertFalse($this->helper->getAutoDisableLayout());
        $this->helper->setAutoDisableLayout(true);
        $this->assertTrue($this->helper->getAutoDisableLayout());
    }

    public function testCanAddContexts()
    {
        $this->helper->addContext('foobar', 'foobar.phtml', 'X-FooBar', 'FooBar');
        $this->assertEquals('foobar.phtml', $this->helper->getSuffix('foobar'));
        $header = $this->helper->getHeader('foobar');
        $this->assertTrue(is_array($header));
        $this->assertTrue(isset($header['type']));
        $this->assertTrue(isset($header['content']));

        $this->assertEquals('X-FooBar', $header['type']);
        $this->assertEquals('FooBar',   $header['content']);
    }

    public function testAddContextThrowsExceptionIfContextAlreadyExists()
    {
        try {
            $this->helper->addContext('xml', 'xml', 'Content-Type', 'application/xml');
            $this->fail('addContext() should raise exception if context already exists');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('already exists', $e->getMessage());
        }
    }

    public function testSetContextOverwritesExistingContext()
    {
        $this->helper->setContext('xml', 'foobar.phtml', 'X-FooBar', 'FooBar');
        $this->assertEquals('foobar.phtml', $this->helper->getSuffix('xml'));
        $header = $this->helper->getHeader('xml');
        $this->assertTrue(is_array($header));
        $this->assertTrue(isset($header['type']));
        $this->assertTrue(isset($header['content']));

        $this->assertEquals('X-FooBar', $header['type']);
        $this->assertEquals('FooBar',   $header['content']);
    }

    public function testCanRemoveContexts()
    {
        $this->helper->removeContext('xml');
        $contexts = $this->helper->getContexts();
        $this->assertFalse(isset($contexts['xml']));
    }

    public function checkNothingIsDone()
    {
        $this->assertEquals('phtml', $this->viewRenderer->getViewSuffix());
        $headers = $this->response->getHeaders();
        $this->assertTrue(empty($headers));
    }

    public function testInitContextDoesNothingIfNoContextsSet()
    {
        unset($this->controller->contexts);
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextDoesNothingIfControllerContextsIsInvalid()
    {
        $this->controller->contexts = 'foo';
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextDoesNothingIfActionHasNoContexts()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('baz');
        $this->helper->initContext();
        $this->checkNothingIsDone();

        $this->request->setParam('format', 'json')
                      ->setActionName('baz');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextDoesNothingIfActionDoesNotHaveContext()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextUsesBooleanTrueActionValueToAssumeAllContexts()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('all');
        $this->helper->initContext();
        $this->assertEquals('json.phtml', $this->viewRenderer->getViewSuffix());

        $this->request->setParam('format', 'xml')
                      ->setActionName('all');
        $this->helper->initContext();
        $this->assertEquals('xml.phtml', $this->viewRenderer->getViewSuffix());
    }

    public function testInitContextDoesNothingIfActionDoesNotHaveContextAndPassedFormatInvalid()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('foo');
        $this->helper->initContext('bogus');
        $this->checkNothingIsDone();
    }

    public function testInitContextSetsViewRendererViewSuffix()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals($this->helper->getSuffix('xml'), $this->viewRenderer->getViewSuffix());
    }

    public function testInitContextSetsAppropriateResponseHeader()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('text/xml', $value);
    }

    public function testInitContextUsesPassedFormatWhenContextParamPresent()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext('json');

        $this->assertEquals('json.phtml', $this->viewRenderer->getViewSuffix());

        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/json', $value);
    }

    public function testInitContextDisablesLayoutByDefault()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertFalse($this->layout->isEnabled());
    }

    public function testInitContextDoesNotDisableLayoutIfDisableLayoutDisabled()
    {
        $this->helper->setAutoDisableLayout(false);
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertTrue($this->layout->isEnabled());
    }

    public function testGetCurrentContextInitiallyNull()
    {
        $this->assertNull($this->helper->getCurrentContext());
    }

    public function testGetCurrentContextReturnsContextAfterInitContextIsSuccessful()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertEquals('xml', $this->helper->getCurrentContext());
    }

    public function testGetCurrentContextResetToNullWhenSubsequentInitContextFails()
    {
        $this->assertNull($this->helper->getCurrentContext());

        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());

        $this->request->setParam('format', 'foo')
                      ->setActionName('bogus');
        $this->helper->initContext();
        $this->assertNull($this->helper->getCurrentContext());
    }

    public function testGetCurrentContextChangesAfterSubsequentInitContextCalls()
    {
        $this->assertNull($this->helper->getCurrentContext());

        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());

        $this->request->setParam('format', 'json')
                      ->setActionName('bar');
        $this->helper->initContext();
        $this->assertEquals('json', $this->helper->getCurrentContext());
    }
}

class Zend_Controller_Action_Helper_ContextSwitchTestController extends Zend_Controller_Action
{
    public $contexts = array(
        'foo' => array('xml'),          // only XML context
        'bar' => array('xml', 'json'),  // only XML and JSON contexts
        'baz' => array(),               // no contexts
        'all' => true,                  // all contexts
    );
}

class Zend_Controller_Action_Helper_ContextSwitchTest_LayoutOverride extends Zend_Layout
{
    public static $_mvcInstance;
}

// Call Zend_Controller_Action_Helper_ContextSwitchTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_ContextSwitchTest::main") {
    Zend_Controller_Action_Helper_ContextSwitchTest::main();
}
