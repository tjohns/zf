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
 * @package    Zend_Registry
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_RegistryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
	}

    public function tearDown()
    {
        $registry = Zend::registry();
        if ($registry !== false) {
            $keys = array();
            foreach ($registry as $key => $value) {
                $keys[] = $key;
            }
            foreach ($keys as $key) {
                $registry->offsetUnset($key);
            }
        }
    }

    public function testBeforeInit()
    {
        $this->assertFalse(Zend::isRegistered('not'));
        $this->assertFalse(Zend::registry('not'));
    }

    public function testManualInit()
    {
        try {
            $registry = Zend::initRegistry('classdoesnotexist');
            $this->fail('Expected exception, because we cannot initialize the registry using a non-existent class.');
        } catch (Zend_Exception $e) {
            $this->assertRegexp('/class.not.found/i', $e->getMessage());
        }

        try {
            $registry = Zend::initRegistry('Zend');
            $this->fail('Expected exception, because we can only initialize the registry using an instance of '
                . 'Zend_Registry (or a subclass).');
        } catch (Zend_Exception $e) {
            $this->assertRegexp('/not.*instanceof.*Zend_Registry/i', $e->getMessage());
        }
    }

    // make sure that the registry can be used without a userland call to create it
    public function testInstance()
    {
        //echo __LINE__, "\n";	

    	// Make sure we get a Zend_Registry object
        #$registry1 = Zend::initRegistry();
    	Zend::register('foo', 'bar');
        $this->assertTrue(Zend::isRegistered('foo'));

        $registry1 = Zend::registry();
    	$this->assertEquals(get_class($registry1), 'Zend_Registry');

    	// should receive a reference to the same object
    	$registry2 = Zend::registry();
    	$this->assertSame($registry1, $registry2);
    	
        // compare existing registry with a duplicate
        $registry4 = new Zend_Registry(array('foo'=>'bar'));
    	$this->assertTrue($registry2 == $registry4);
    	
    	// make sure these are not the same, since one is empty, and the other is not
    	$this->assertNotSame($registry1, new Zend_Registry());
    }

    public function testInit()
    {
        // re-initialization is not permitted
        try {
        	$registry3 = Zend::initRegistry(new Zend_Registry());
            $this->fail('Expected exception, because re-initialization is not permitted.');
        } catch (Zend_Exception $e) {
            $this->assertRegexp('/already.initializ/i', $e->getMessage());
        }
    }

    public function testBadIndex()
    {
        try {
        	Zend::registry('foobar');
            $this->fail('Expected exception when trying to fetch a non-existent key.');
        } catch (Zend_Exception $e) {
            $this->assertRegexp('/no.key/i', $e->getMessage());
        }
    }

    // test tearDown()
    public function testTearDown()
    {
        $empty = new Zend_Registry();
        $this->assertTrue(Zend::registry() == $empty);
        $this->assertTrue(Zend::registry()->count() === 0);
    }

    // make sure registry is not the same as a different instance
    public function testRegistryEmptyReturnsArray()
    {
    	$registry = new Zend_Registry();
        $this->assertNotSame($registry, new ArrayObject());

    	$registry = new Zend_Registry(array('foo', 'bar'));
        $this->assertNotSame($registry, Zend::registry());

        Zend::register('foo', 'bar');
        $this->assertNotSame($registry, Zend::registry());
    }

    /**
     * Tests that:
     *   1. an object can be registered with register().
     *   2. the object is returned by registry('objectName').
     *   3. the object is listed in the ArrayObject returned by registry().
     *   4. isRegistered() returns correct states.
     */    
    public function testRegistry()
    {
    	$registry = Zend::registry();
    	
        $this->assertFalse($registry->offsetExists('objectName'));
        
        $subregistry = new Zend_Registry(array('option1' => 'setting1', 'option2' => 'setting2'));
        
        // throws exception on failure
        Zend::register('componentOptions', $subregistry);

        $this->assertTrue($registry->offsetExists('componentOptions'));
        
        // compare fetched value with the expected value
        $this->assertSame(Zend::registry('componentOptions'), $subregistry);

        $this->assertTrue($registry->offsetGet('componentOptions') 
            == new Zend_Registry(array('option1' => 'setting1', 'option2' => 'setting2')));
        
        // Make sure a second object can be registered
        $object2 = new stdClass();
        $this->assertNotSame($subregistry,$object2);
        
        // throws exception on failure
        $registry->offsetSet('componentOptions', $object2);

        $this->assertTrue($registry->offsetExists('componentOptions'));

        $this->assertNotSame(Zend::registry('componentOptions'), $subregistry);

        $this->assertSame(Zend::registry('componentOptions'), $object2);
    }

}
