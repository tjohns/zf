<?php
// Call Zend_Controller_Action_Helper_ActionStackTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_ActionStackTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Action/Helper/ActionStack.php';
require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_Controller_Action_Helper_ActionStack.
 */
class Zend_Controller_Action_Helper_ActionStackTest extends PHPUnit_Framework_TestCase 
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_ActionStackTest");
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

    public function testConstructorInstantiatesPluginIfNotPresent()
    {
        $front = Zend_Controller_Front::getInstance();
        $this->assertFalse($front->hasPlugin('Zend_Controller_Plugin_ActionStack'));
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $this->assertTrue($front->hasPlugin('Zend_Controller_Plugin_ActionStack'));
    }

    public function testConstructorUsesExistingPluginWhenPresent()
    {
        $front  = Zend_Controller_Front::getInstance();
        $plugin = new Zend_Controller_Plugin_ActionStack();
        $front->registerPlugin($plugin);
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $this->assertTrue($front->hasPlugin('Zend_Controller_Plugin_ActionStack'));
        $registered = $front->getPlugin('Zend_Controller_Plugin_ActionStack');
        $this->assertSame($plugin, $registered);
    }

    /**
     * @todo Implement testPushStack().
     */
    public function testPushStack()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testActionToStack().
     */
    public function testActionToStack()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDirect().
     */
    public function testDirect()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}

// Call Zend_Controller_Action_Helper_ActionStackTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_ActionStackTest::main") {
    Zend_Controller_Action_Helper_ActionStackTest::main();
}
