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
     * Tests that a class can be loaded from a well-formed PHP file
     */
    public function testLoadInterfaceValid()
    {
        $dir = implode(array(dirname(__FILE__), 'Zend', '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        Zend::loadInterface('Interface1', $dir);
    }

    /**
     * Tests that an exception is thrown when a file is loaded but the
     * class is not found within the file
     */
    public function testLoadInterfaceNonexistent()
    {
        $dir = implode(array(dirname(__FILE__), 'Zend', '_files', '_testDir1'), DIRECTORY_SEPARATOR);

        try {
            Zend::loadInterface('ClassNonexistent', $dir);
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/file(.*)loaded but interface(.*)not found/i', $e->getMessage());
            return;
        }
        $this->fail('Zend_Exception was expected but never thrown.');
    }

    /**
     * @todo testing for Zend::dump()
     */

    /**
     * Tests that register() throws an exception when the name of
     * the object to register is not a string.
     */
    public function testRegisterNameNotString()
    {
        try {
            Zend::register(new stdClass(), 'test');
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/must be a string/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown, expected Zend_Exception.');
    }

    /**
     * Tests that register() throws an exception when the second
     * argument (the object) is not an object.
     */
    public function testRegisterObjNotObject()
    {
        try {
            Zend::register('test', null);
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/only objects/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown, expected Zend_Exception.');
    }

    /**
     * Tests that registry() with no arguments return array()
     * when the registry is empty.
     */
    public function testRegistryEmptyReturnsArray()
    {
        $this->assertSame(Zend::registry(), array());
    }

    /**
     * Tests that:
     *   1. an object can be registered with register().
     *   2. attempting to register the same object throws an exception.
     *   3. the object is returned by registry('objectName').
     *   4. the object is listed in the array returned by registry().
     *   5. isRegistered() returns correct states.
     */
    public function testRegistry()
    {
        $this->assertFalse(Zend::isRegistered('objectName'));
        
        /**
         * Register an object
         */
        $obj = new stdClass();
        // throws exception on failure
        Zend::register('objectName', $obj);

        $this->assertTrue(Zend::isRegistered('objectName'));
        
        /**
         * Attempt to register the same object again
         */
        $e = null;
        try {
            Zend::register('another', $obj);
        } catch (Zend_Exception $e) {
            $this->assertRegExp('/duplicate(.*)objectName/i', $e->getMessage());
        }

        if ($e === null) {
            $this->fail('No exception thown during registration of duplicate object.');
        }

        /**
         * Attempt to retrieve the object with registry()
         */
        $this->assertSame(Zend::registry('objectName'), $obj);

        /**
         * Check registry listing
         */
        $this->assertEquals(Zend::registry(), array('objectName' => 'stdClass'));
    }

}
