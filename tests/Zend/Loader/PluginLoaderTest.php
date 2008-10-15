<?php
// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Loader_PluginLoaderTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

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
        $this->key = null;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->clearStaticPaths();
    }

    public function clearStaticPaths()
    {
        if (null !== $this->key) {
            $loader = new Zend_Loader_PluginLoader(array(), $this->key);
            $loader->clearPaths();
        }
    }

    public function testAddPrefixPathNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, count($paths['Zend_View_']));
        $this->assertEquals(2, count($paths['Zend_Loader_']));
    }

    public function testAddPrefixPathStatically()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, count($paths['Zend_View_']));
        $this->assertEquals(2, count($paths['Zend_Loader_']));
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPrefix()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $loader->addPrefixPath(array(), $this->libPath);
            $this->fail('addPrefixPath() should throw exception with non-string prefix');
        } catch (Exception $e) {
        }
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPath()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $loader->addPrefixPath('Foo_Bar', array());
            $this->fail('addPrefixPath() should throw exception with non-string path');
        } catch (Exception $e) {
        }
    }

    public function testRemoveAllPathsForGivenPrefixNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths('Zend_Loader');
        $this->assertEquals(2, count($paths));
        $loader->removePrefixPath('Zend_Loader');
        $this->assertFalse($loader->getPaths('Zend_Loader'));
    }

    public function testRemoveAllPathsForGivenPrefixStatically()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths('Zend_Loader');
        $this->assertEquals(2, count($paths));
        $loader->removePrefixPath('Zend_Loader');
        $this->assertFalse($loader->getPaths('Zend_Loader'));
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixNotRegistered()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $loader->removePrefixPath('Foo_Bar');
            $this->fail('Removing non-existent prefix should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixPathPairNotRegistered()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Bar', realpath(dirname(__FILE__)));
        $paths = $loader->getPaths();
        $this->assertTrue(isset($paths['Foo_Bar_']));
        try {
            $loader->removePrefixPath('Foo_Bar', $this->libPath);
            $this->fail('Removing non-existent prefix/path pair should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testClearPathsNonStaticallyClearsPathArray()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths();
        $paths = $loader->getPaths();
        $this->assertEquals(0, count($paths));
    }

    public function testClearPathsStaticallyClearsPathArray()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths();
        $paths = $loader->getPaths();
        $this->assertEquals(0, count($paths));
    }

    public function testClearPathsWithPrefixNonStaticallyClearsPathArray()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths('Zend_Loader');
        $paths = $loader->getPaths();
        $this->assertEquals(1, count($paths));
    }

    public function testClearPathsWithPrefixStaticallyClearsPathArray()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View', $this->libPath . '/Zend/View')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend/Loader')
               ->addPrefixPath('Zend_Loader', $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, count($paths));
        $loader->clearPaths('Zend_Loader');
        $paths = $loader->getPaths();
        $this->assertEquals(1, count($paths));
    }

    public function testGetClassNameNonStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testGetClassNameStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testLoadPluginNonStaticallyLoadsClass()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormButton');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zend_View_Helper_FormButton', $className);
        $this->assertTrue(class_exists('Zend_View_Helper_FormButton', false));
        $this->assertTrue($loader->isLoaded('FormButton'));
    }

    public function testLoadPluginStaticallyLoadsClass()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormRadio');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals('Zend_View_Helper_FormRadio', $className);
        $this->assertTrue(class_exists('Zend_View_Helper_FormRadio', false));
        $this->assertTrue($loader->isLoaded('FormRadio'));
    }

    public function testLoadThrowsExceptionIfFileFoundInPrefixButClassNotLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('DocType');
            $this->fail('Invalid prefix for a path should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testLoadThrowsExceptionIfNoHelperClassLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FooBarBazBat');
            $this->fail('Not finding a helper should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testGetClassAfterNonStaticLoadReturnsResolvedClassName()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormSelect');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSelect'));
        $this->assertEquals('Zend_View_Helper_FormSelect', $loader->getClassName('FormSelect'));
    }

    public function testGetClassAfterStaticLoadReturnsResolvedClassName()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader(array(), $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        try {
            $className = $loader->load('FormCheckbox');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormCheckbox'));
        $this->assertEquals('Zend_View_Helper_FormCheckbox', $loader->getClassName('FormCheckbox'));
    }

    public function testClassFilesAreSearchedInLifoOrder()
    {
        $loader = new Zend_Loader_PluginLoader(array());
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', dirname(__FILE__) . '/_files/ZfTest');
        try {
            $className = $loader->load('FormSubmit');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSubmit'));
        $this->assertEquals('ZfTest_FormSubmit', $loader->getClassName('FormSubmit'));
    }
    
    /**
     * @issue ZF-2741
     */
    public function testWin32UnderscoreSpacedShortNamesWillLoad()
    {
        $loader = new Zend_Loader_PluginLoader(array());
        $loader->addPrefixPath('Zend_Filter', $this->libPath . '/Zend/Filter');
        try {
            // Plugin loader will attempt to load "c:\path\to\library/Zend/Filter/Word\UnderscoreToDash.php"
            $className = $loader->load('Word_UnderscoreToDash');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf("Failed loading helper; paths: %s", var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('Word_UnderscoreToDash'));
    }
    
}

// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD === 'Zend_Loader_PluginLoaderTest::main') {
    Zend_Loader_PluginLoaderTest::main();
}
