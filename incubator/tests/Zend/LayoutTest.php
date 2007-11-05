<?php
// Call Zend_LayoutTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(__FILE__)) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_LayoutTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Layout.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/View/Interface.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Layout.
 */
class Zend_LayoutTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_LayoutTest");
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
        Zend_Controller_Front::getInstance()->resetInstance();
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        }
        if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        }
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

    public function testDefaultLayoutStatusAtInitialization()
    {
        $layout = new Zend_Layout();
        $this->assertEquals('layout', $layout->getLayout());
        $this->assertEquals('content', $layout->getContentKey());
        $this->assertTrue($layout->isEnabled());
        $this->assertTrue($layout->inflectorEnabled());
        $this->assertNull($layout->getLayoutPath());
        $this->assertTrue($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testSetConfigModifiesAttributes()
    {
        $layout = new Zend_Layout();

        require_once 'Zend/Config.php';
        $config = new Zend_Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => dirname(__FILE__),
            'mvcEnabled'       => false,
        ));
        $layout->setConfig($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(dirname(__FILE__), $layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testLayoutAccessorsModifyAndRetrieveLayoutValue()
    {
        $layout = new Zend_Layout();
        $layout->setLayout('foo');
        $this->assertEquals('foo', $layout->getLayout());
    }

    /**
     * @return void
     */
    public function testSetLayoutEnablesLayouts()
    {
        $layout = new Zend_Layout();
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
        $layout->setLayout('foo');
        $this->assertTrue($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testDisableLayoutDisablesLayouts()
    {
        $layout = new Zend_Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testEnableLayoutEnablesLayouts()
    {
        $layout = new Zend_Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
        $layout->enableLayout();
        $this->assertTrue($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testLayoutPathAccessorsWork()
    {
        $layout = new Zend_Layout();
        $layout->setLayoutPath(dirname(__FILE__));
        $this->assertEquals(dirname(__FILE__), $layout->getLayoutPath());
    }

    /**
     * @return void
     */
    public function testContentKeyAccessorsWork()
    {
        $layout = new Zend_Layout();
        $layout->setContentKey('foo');
        $this->assertEquals('foo', $layout->getContentKey());
    }

    /**
     * @return void
     */
    public function testMvcEnabledAccessorsWork()
    {
        $layout = new Zend_Layout();
        $layout->setMvcEnabled(false);
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testGetViewRetrievesViewWhenNoneSet()
    {
        $layout = new Zend_Layout();
        $view = $layout->getView();
        $this->assertTrue($view instanceof Zend_View_Interface);
    }

    /**
     * @return void
     */
    public function testGetViewRetrievesViewFromViewRenderer()
    {
        $layout = new Zend_Layout();
        $view = $layout->getView();
        $vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->assertSame($vr->view, $view);
    }

    /**
     * @return void
     */
    public function testViewAccessorsAllowSettingView()
    {
        $layout = new Zend_Layout();
        $view   = new Zend_View();
        $layout->setView($view);
        $received = $layout->getView();
        $this->assertSame($view, $received);
    }

    /**
     * @todo Implement testSetInflector().
     */
    public function testSetInflector()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetInflector().
     */
    public function testGetInflector()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testEnableInflector().
     */
    public function testEnableInflector()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDisableInflector().
     */
    public function testDisableInflector()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testInflectorEnabled().
     */
    public function testInflectorEnabled()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement test__set().
     */
    public function test__set()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement test__get().
     */
    public function test__get()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement test__isset().
     */
    public function test__isset()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement test__unset().
     */
    public function test__unset()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testAssign().
     */
    public function testAssign()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testRender().
     */
    public function testRender()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}

// Call Zend_LayoutTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_LayoutTest::main") {
    Zend_LayoutTest::main();
}
