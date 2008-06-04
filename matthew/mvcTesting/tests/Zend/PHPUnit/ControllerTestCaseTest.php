<?php
// Call Zend_PHPUnit_ControllerTestCaseTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_PHPUnit_ControllerTestCaseTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_PHPUnit_ControllerTestCase */
require_once 'Zend/PHPUnit/ControllerTestCase.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Test class for Zend_PHPUnit_ControllerTestCase.
 */
class Zend_PHPUnit_ControllerTestCaseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_PHPUnit_ControllerTestCaseTest");
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
        $this->testCase = new Zend_PHPUnit_ControllerTestCase();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $registry = Zend_Registry::getInstance();
        if (isset($registry['router'])) {
            unset($registry['router']);
        }
        if (isset($registry['dispatcher'])) {
            unset($registry['dispatcher']);
        }
        if (isset($registry['plugin'])) {
            unset($registry['plugin']);
        }
        if (isset($registry['viewRenderer'])) {
            unset($registry['viewRenderer']);
        }
    }

    public function testGetFrontControllerShouldReturnFrontController()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
    }

    public function testGetFrontControllerShouldReturnSameFrontControllerObjectOnRepeatedCalls()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
        $test = $this->testCase->getFrontController();
        $this->assertSame($controller, $test);
    }

    public function testGetRequestShouldReturnRequestTestCase()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
    }

    public function testGetRequestShouldReturnSameRequestObjectOnRepeatedCalls()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
        $test = $this->testCase->getRequest();
        $this->assertSame($request, $test);
    }

    public function testGetResponseShouldReturnResponseTestCase()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
    }

    public function testGetResponseShouldReturnSameResponseObjectOnRepeatedCalls()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
        $test = $this->testCase->getResponse();
        $this->assertSame($response, $test);
    }

    public function testGetQueryShouldReturnQueryTestCase()
    {
        $query = $this->testCase->getQuery();
        $this->assertTrue($query instanceof Zend_Dom_Query);
    }

    public function testGetQueryShouldReturnSameQueryObjectOnRepeatedCalls()
    {
        $query = $this->testCase->getQuery();
        $this->assertTrue($query instanceof Zend_Dom_Query);
        $test = $this->testCase->getQuery();
        $this->assertSame($query, $test);
    }

    public function testResetShouldResetMvcState()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        $request    = $this->testCase->getRequest();
        $response   = $this->testCase->getResponse();
        $router     = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin     = new Zend_Controller_Plugin_ErrorHandler();
        $controller = $this->testCase->getFrontController();
        $controller->setParam('foo', 'bar')
                   ->registerPlugin($plugin)
                   ->setRouter($router)
                   ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->testCase->reset();
        $test = $controller->getRouter();
        $this->assertNotSame($router, $test);
        $test = $controller->getDispatcher();
        $this->assertNotSame($dispatcher, $test);
        $this->assertFalse($controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $test = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->assertNotSame($viewRenderer, $test);
        $this->assertNull($controller->getRequest());
        $this->assertNull($controller->getResponse());
        $this->assertNotSame($request, $this->testCase->getRequest());
        $this->assertNotSame($response, $this->testCase->getResponse());
    }

    public function testBootstrapShouldSetRequestAndResponseTestCaseObjects()
    {
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $request    = $controller->getRequest();
        $response   = $controller->getResponse();
        $this->assertSame($this->testCase->getRequest(), $request);
        $this->assertSame($this->testCase->getResponse(), $response);
    }

    public function testBootstrapShouldIncludeBootstrapFileSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = dirname(__FILE__) . '/_files/bootstrap.php';
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function testBootstrapShouldInvokeCallbackSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = array($this, 'bootstrapCallback');
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function bootstrapCallback()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Front.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        require_once 'Zend/Registry.php';
        $router     = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin     = new Zend_Controller_Plugin_ErrorHandler();
        $controller = Zend_Controller_Front::getInstance();
        $controller->setParam('foo', 'bar')
                   ->registerPlugin($plugin)
                   ->setRouter($router)
                   ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        Zend_Registry::set('router', $router);
        Zend_Registry::set('dispatcher', $dispatcher);
        Zend_Registry::set('plugin', $plugin);
        Zend_Registry::set('viewRenderer', $viewRenderer);
    }

    public function testDispatchShouldDispatchSpecifiedUrl()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/bar');
        $request  = $this->testCase->getRequest();
        $response = $this->testCase->getResponse();
        $content  = $response->getBody();
        $this->assertEquals('foo', $request->getControllerName());
        $this->assertEquals('bar', $request->getActionName());
        $this->assertContains('FooController::barAction', $content, $content);
    }

    public function testAssertSelectShouldDoNothingForValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        $this->testCase->assertSelect('div#foo legend.bar');
        $this->testCase->assertSelect('div#foo legend.baz');
        $this->testCase->assertSelect('div#foo legend.bat');
        $this->testCase->assertNotSelect('div#foo legend.bogus');
        $this->testCase->assertSelectContentContains('legend.bat', 'La di da');
        $this->testCase->assertNotSelectContentContains('legend.bat', 'La do da');
        $this->testCase->assertSelectContentRegex('legend.bat', '/d[a|i]/i');
        $this->testCase->assertNotSelectContentRegex('legend.bat', '/d[o|e]/i');
        $this->testCase->assertSelectCountMin('div#foo legend.bar', 2);
        $this->testCase->assertSelectCount('div#foo legend.bar', 2);
        $this->testCase->assertSelectCountMin('div#foo legend.bar', 2);
        $this->testCase->assertSelectCountMax('div#foo legend.bar', 2);
    }

    public function testAssertSelectShouldThrowExceptionsForInValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        try {
            $this->testCase->assertNotSelect('div#foo legend.bar');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelect('div#foo legend.bogus');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotSelectContentContains('legend.bat', 'La di da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelectContentContains('legend.bat', 'La do da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotSelectContentRegex('legend.bat', '/d[a|i]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelectContentRegex('legend.bat', '/d[o|e]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelectCountMin('div#foo legend.bar', 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelectCount('div#foo legend.bar', 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelectCountMin('div#foo legend.bar', 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertSelectCountMax('div#foo legend.bar', 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
    }

    public function testAssertXpathShouldDoNothingForValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' bar ')]");
        $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' baz ')]");
        $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' bat ')]");
        $this->testCase->assertNotXpath("//div[@id='foo']//legend[contains(@class, ' bogus ')]");
        $this->testCase->assertXpathContentContains("//legend[contains(@class, ' bat ')]", "La di da");
        $this->testCase->assertNotXpathContentContains("//legend[contains(@class, ' bat ')]", "La do da");
        $this->testCase->assertXpathContentRegex("//legend[contains(@class, ' bat ')]", "/d[a'i]/i");
        $this->testCase->assertNotXpathContentRegex("//legend[contains(@class, ' bat ')]", "/d[o'e]/i");
        $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->testCase->assertXpathCount("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->testCase->assertXpathCountMax("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
    }

    public function testAssertXpathShouldThrowExceptionsForInValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        try {
            $this->testCase->assertNotXpath("//div[@id='foo']//legend[contains(@class, ' bar ')]");
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' bogus ')]");
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotXpathContentContains("//legend[contains(@class, ' bat ')]", "La di da");
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathContentContains("//legend[contains(@class, ' bat ')]", 'La do da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotXpathContentRegex("//legend[contains(@class, ' bat ')]", '/d[a|i]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathContentRegex("//legend[contains(@class, ' bat ')]", '/d[o|e]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCount("//div[@id='foo']//legend[contains(@class, ' bar ')]", 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCountMax("//div[@id='foo']//legend[contains(@class, ' bar ')]", 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_PHPUnit_Constraint_Exception $e) {
        }
    }

    public function testRedirectAssertionsShouldDoNothingForValidAssertions()
    {
        $this->testCase->getResponse()->setRedirect('/foo');
        $this->testCase->assertRedirect();
        $this->testCase->assertRedirectTo('/foo', var_export($this->testCase->getResponse()->sendHeaders(), 1));
        $this->testCase->assertRedirectRegex('/FOO$/i');

        $this->testCase->reset();
        $this->testCase->assertNotRedirect();
        $this->testCase->assertNotRedirectTo('/foo');
        $this->testCase->assertNotRedirectRegex('/FOO$/i');
        $this->testCase->getResponse()->setRedirect('/foo');
        $this->testCase->assertNotRedirectTo('/bar');
        $this->testCase->assertNotRedirectRegex('/bar/i');
    }
}

// Call Zend_PHPUnit_ControllerTestCaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_PHPUnit_ControllerTestCaseTest::main") {
    Zend_PHPUnit_ControllerTestCaseTest::main();
}
