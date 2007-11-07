<?php
// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Loader_PluginLoaderTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Loader/PluginLoader.php';

/**
 * Test class for Zend_Loader_PluginLoader.
 */
class Zend_Loader_PluginLoaderTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Loader_PluginLoaderTest");
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
        $this->libPath = realpath(dirname(__FILE__) . '/../../../library');
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

    public function clearStaticPaths($key) 
    {
        $loader = new Zend_Loader_PluginLoader(array(), $key);
        $loader->clearPaths();
    }

    public function testAddPrefixPathNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, count($paths['Zend_View_']));
        $this->assertEquals(1, count($paths['Zend_Loader_']));
    }

    /**
     * @todo Implement testRemovePrefixPath().
     */
    public function testRemovePrefixPath()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testIsLoaded().
     */
    public function testIsLoaded()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetClassName().
     */
    public function testGetClassName()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testLoad().
     */
    public function testLoad()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}

// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Loader_PluginLoaderTest::main") {
    Zend_Loader_PluginLoaderTest::main();
}
