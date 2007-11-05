<?php
// Call Zend_LayoutTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(__FILE__)) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_LayoutTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Layout.php';

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
     * @todo Implement testSetConfig().
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
     * @todo Implement testSetLayout().
     */
    public function testSetLayout()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetLayout().
     */
    public function testGetLayout()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDisableLayout().
     */
    public function testDisableLayout()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testEnableLayout().
     */
    public function testEnableLayout()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testIsEnabled().
     */
    public function testIsEnabled()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetLayoutPath().
     */
    public function testSetLayoutPath()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetLayoutPath().
     */
    public function testGetLayoutPath()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetContentKey().
     */
    public function testSetContentKey()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetContentKey().
     */
    public function testGetContentKey()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetMvcEnabled().
     */
    public function testSetMvcEnabled()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetMvcEnabled().
     */
    public function testGetMvcEnabled()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetView().
     */
    public function testSetView()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetView().
     */
    public function testGetView()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
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
