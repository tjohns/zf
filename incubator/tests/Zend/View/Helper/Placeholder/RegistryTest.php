<?php
// Call Zend_View_Helper_Placeholder_RegistryTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    $base = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
    $paths = array(
        $base . '/incubator/tests',
        $base . '/incubator/library',
        $base . '/library'
    );
    set_include_path(implode(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_Placeholder_RegistryTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/**
 * Test class for Zend_View_Helper_Placeholder_Registry.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_Placeholder_RegistryTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_Placeholder_RegistryTest");
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

    /**
     * @todo Implement testCreateContainer().
     */
    public function testCreateContainer()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetContainer().
     */
    public function testGetContainer()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetContainer().
     */
    public function testSetContainer()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetContainerClass().
     */
    public function testSetContainerClass()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetContainerClass().
     */
    public function testGetContainerClass()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}

// Call Zend_View_Helper_Placeholder_RegistryTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_Placeholder_RegistryTest::main") {
    Zend_View_Helper_Placeholder_RegistryTest::main();
}
