<?php
/**
 * @package    Zend
 * @subpackage UnitTests
 */


/**
 * Zend
 */
require_once 'Zend.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * Unit testing for the Zend class.
 *
 * @package    Zend
 * @subpackage UnitTests
 */
class ZendTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that a class can be loaded from a well-formed PHP file
     */
    public function testLoadClassValid()
    {
        $dir = implode(array(dirname(__FILE__), 'Zend', '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        Zend::loadClass('Class1', $dir);
    }

    /**
     * Tests that an exception is thrown when a file is loaded but the
     * class is not found within the file
     */
    public function testLoadClassNonexistent()
    {
        $dir = implode(array(dirname(__FILE__), 'Zend', '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        try {
            Zend::loadClass('ClassNonexistent', $dir);
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/file(.*)loaded but class(.*)not found/i', $e->getMessage());
            return;
        }
        $this->fail('Zend_Exception was expected but never thrown.');
    }

    /**
     * Tests that an exception is thrown when the file is not found.
     */
    public function testLoadClassFileNotFound()
    {
        try {
            Zend::loadClass('Zend_File_Not_Found', '');
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/file(.*)not found/i', $e->getMessage());
            return;
        }
    }

    /**
     * Tests that a class can be loaded from the search directories.
     */
    public function testLoadClassSearchDirs()
    {
        $dirs = array();
        foreach (array('_testDir1', '_testDir2') as $dir) {
            $dirs[] = implode(array(dirname(__FILE__), 'Zend', '_files', $dir), DIRECTORY_SEPARATOR);
        }

        // throws exception on failure
        Zend::loadClass('Class1', $dirs);
        Zend::loadClass('Class2', $dirs);
    }

    /**
     * Tests that the security filter catches directory injections.
     */
    public function testLoadClassIllegalFilename()
    {
        try {
            Zend::loadClass('/path/to/danger');
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/security(.*)filename/i', $e->getMessage());
            return;
        }
        $this->assertFail('Zend_Exception was expected but never thrown.');
    }

    /**
     * @todo testing for Zend::dump()
     */

    /**
     * Registry related tests are found in Zend/Registry/.
     */

    /**
     * Tests that isReadable works
     */
    public function testIsReadable()
    {
        $this->assertTrue(Zend::isReadable(__FILE__));
        $this->assertFalse(Zend::isReadable(__FILE__ . '.foobaar'));
    }

    /**
     * Tests that:
     *   1. returns a valid Exception object when given a valid exception class
     *   2. throws an exception when in invalid class is provided
     */
    public function testException()
    {
        $this->assertTrue(Zend::exception('Zend_Exception') instanceof Exception);

        try {
            $e = Zend::exception('Zend_FooBar_Baz', 'should fail');
            $this->fail('invalid exception class should throw exception');
        } catch (Exception $e) {
            // success...
        }
    }
}
