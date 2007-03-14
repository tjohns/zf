<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Unit testing for the Zend_Loader class.
 *
 * @package    Zend_Loader
 * @subpackage UnitTests
 */
class Zend_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that a class can be loaded from a well-formed PHP file
     */
    public function testLoaderClassValid()
    {
        $dir = implode(array(dirname(__FILE__), '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        Zend_Loader::loadClass('Class1', $dir);
    }

    public function testLoaderInterfaceViaLoadClass()
    {
        try {
            Zend_Loader::loadClass('Zend_Controller_Dispatcher_Interface');
        } catch (Exception $e) {
            $this->fail('Loading interfaces should not fail');
        }
    }

    /**
     * Tests that an exception is thrown when a file is loaded but the
     * class is not found within the file
     */
    public function testLoaderClassNonexistent()
    {
        $dir = implode(array(dirname(__FILE__), '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        try {
            Zend_Loader::loadClass('ClassNonexistent', $dir);
            $this->fail('Zend_Exception was expected but never thrown.');
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/file(.*)loaded but class(.*)not found/i', $e->getMessage());
        }
    }

    /**
     * Tests that an exception is thrown when the file is not found.
     */
    public function testLoaderClassFileNotFound()
    {
        try {
            Zend_Loader::loadClass('Zend_File_Not_Found', '');
            $this->fail('Zend_Exception was expected but never thrown.');
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/file(.*)not found/i', $e->getMessage());
        }
    }

    /**
     * Tests that a class can be loaded from the search directories.
     */
    public function testLoaderClassSearchDirs()
    {
        $dirs = array();
        foreach (array('_testDir1', '_testDir2') as $dir) {
            $dirs[] = implode(array(dirname(__FILE__), '_files', $dir), DIRECTORY_SEPARATOR);
        }

        // throws exception on failure
        Zend_Loader::loadClass('Class1', $dirs);
        Zend_Loader::loadClass('Class2', $dirs);
    }

    /**
     * Tests that a class locatedin a subdirectory can be loaded from the search directories
     */
    public function testLoaderClassSearchSubDirs()
    {
        $dirs = array();
        foreach (array('_testDir1', '_testDir2') as $dir) {
            $dirs[] = implode(array(dirname(__FILE__), '_files', $dir), DIRECTORY_SEPARATOR);
        }

        // throws exception on failure
        Zend_Loader::loadClass('Class1_Subclass2', $dirs);
    }

    /**
     * Tests that the security filter catches directory injections.
     */
    public function testLoaderClassIllegalFilename()
    {
        try {
            Zend_Loader::loadClass('/path/to/danger');
            $this->fail('Zend_Exception was expected but never thrown.');
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/security(.*)filename/i', $e->getMessage());
        }
    }

    /**
     * Tests that isReadable works
     */
    public function testLoaderIsReadable()
    {
        $this->assertTrue(Zend_Loader::isReadable(__FILE__));
        $this->assertFalse(Zend_Loader::isReadable(__FILE__ . '.foobaar'));
    }

    /**
     * Tests that autoload works for valid classes and interfaces
     */
    public function testAutoloadLoadsValidClasses()
    {
        $this->assertEquals('Zend_Db_Profiler_Exception', Zend_Loader::autoload('Zend_Db_Profiler_Exception'));
        $this->assertEquals('Zend_Auth_Storage_Interface', Zend_Loader::autoload('Zend_Auth_Storage_Interface'));
    }

    /**
     * Tests that autoload returns false on invalid classes
     */
    public function testAutoloadFailsOnInvalidClasses()
    {
        $this->assertFalse(Zend_Loader::autoload('Zend_FooBar_Magic_Abstract'));
    }
}
