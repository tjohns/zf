<?php
/**
 * @package    Zend_Registry
 * @subpackage UnitTests
 */

/**
 * Zend_Registry
 */
require_once 'Zend.php';
require_once 'Zend/Registry.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Registry
 * @subpackage UnitTests
 */
class Zend_Registry_RegistryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    	Zend_Registry::setInstance(NULL);
	}
    public function tearDown()
    {
    }
    /**
     * Tests that a registry object is automatically created
     * when getInstance is called for the first time
     */
    public function testInstance()
    {
    	/* Make sure get a Zend_Registry object */
    	$registry1 = Zend_Registry::getInstance();	
    	$this->assertEquals(get_class($registry1),'Zend_Registry');
    	
    	/* And should get the same object again */
    	$registry2 = Zend_Registry::getInstance();    	
    	$this->assertSame($registry1,$registry2);
    	
    	/* Change the registry instance */
    	$registry3 = new Zend_Registry();
    	Zend_Registry::setInstance($registry3);
    	
    	$registry4 = Zend_Registry::getInstance();    	
    	$this->assertSame($registry3,$registry4);
    	
    	/* Just to be sure have a different object */
    	$this->assertNotSame($registry1,$registry4);
    	
    }
    /**
     * Tests that register() throws an exception when the name of
     * the object to register is not a string.
     */
    public function testRegisterNameNotString()
    {
    	$registry = new Zend_Registry();
    	
        try {
            $registry->set(new stdClass(), 'test');
        } catch (Zend_Registry_Exception $e) {
            $this->assertRegExp('/must be a string/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown, expected Zend_Registry_Exception.');
    }

    /**
     * Tests that register() throws an exception when the second
     * argument (the object) is not an object.
     */
    public function testRegisterObjNotObject()
    {
    	$registry = new Zend_Registry();

        try {
            $registry->set('test', null);
        } catch (Zend_Registry_Exception $e) {
            $this->assertRegExp('/only objects/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown, expected Zend_Registry_Exception.');
    }

    /**
     * Tests that registry() with no arguments return array()
     * when the registry is empty.
     */
    public function testRegistryEmptyReturnsArray()
    {
    	$registry = new Zend_Registry();
    	
        $this->assertSame($registry->get(), array());
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
    	$registry = new Zend_Registry();
    	
        $this->assertFalse($registry->has('objectName'));
        
        /**
         * Register an object
         */
        $obj = new stdClass();
        //$obj->name = 'Name1';
        
        // throws exception on failure
        $registry->set('objectName', $obj);

        $this->assertTrue($registry->has('objectName'));
        
        /**
         * Attempt to register the same object again
         */
        $e = null;
        try {
            $registry->set('another', $obj);
        } catch (Zend_Registry_Exception $e) {
            $this->assertRegExp('/duplicate(.*)objectName/i', $e->getMessage());
        }

        if ($e === null) {
            $this->fail('No exception thown during registration of duplicate object.');
        }

        /**
         * Attempt to retrieve the object with registry()
         */
        $this->assertSame($registry->get('objectName'), $obj);

        /**
         * Check registry listing
         */
        $this->assertEquals($registry->get(), array('objectName' => 'stdClass'));
        
        /**
         * Make sure a second object can be registered
         */
        $obj2 = new stdClass();
        //$obj2->name = 'Name1';
        $this->assertNotSame($obj,$obj2);
        
        // throws exception on failure
        $registry->set('objectName2', $obj2);

        $this->assertTrue($registry->has('objectName2'));
    	
    }
    /* -----------------------------------------------------------
     * Same tests using static interface
     */
     
    /**
     * Tests that register() throws an exception when the name of
     * the object to register is not a string.
     */
    public function testStaticRegisterNameNotString()
    {
        try {
            Zend::register(new stdClass(), 'test');
        } catch (Zend_Registry_Exception $e) {
            $this->assertRegExp('/must be a string/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown, expected Zend_Registry_Exception.');
    }

    /**
     * Tests that register() throws an exception when the second
     * argument (the object) is not an object.
     */
    public function testStaticRegisterObjNotObject()
    {
        try {
            Zend::register('test', null);
        } catch (Zend_Registry_Exception $e) {
            $this->assertRegExp('/only objects/i', $e->getMessage());
            return;
        }
        $this->fail('No exception thrown, expected Zend_Registry_Exception.');
    }

    /**
     * Tests that registry() with no arguments return array()
     * when the registry is empty.
     */
    public function testStaticRegistryEmptyReturnsArray()
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
    public function testStaticRegistry()
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
        } catch (Zend_Registry_Exception $e) {
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
