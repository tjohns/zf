<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_LayoutTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Layout.php';

/**
 * Test class for Zend_Layout
 */
class Zend_LayoutTest extends PHPUnit_Framework_TestCase 
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
        $this->layout = new Zend_Layout();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->layout);
    }

    /**
     * @return void
     */
    public function testDefaultLayoutExists()
    {
        $this->markTestIncomplete('Test for default layout incomplete');
    }

    /**
     * @return void
     */
    public function testChangingLayoutIsPossible()
    {
        $this->markTestIncomplete('Test for changing layout incomplete');
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_LayoutTest::main") {
    Zend_LayoutTest::main();
}
