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
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Measure_Acceleration
 */
require_once 'Zend/Measure/Acceleration.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_AccelerationTest extends PHPUnit_Framework_TestCase
{

    /**
     * test for acceleration initialisation
     * expected instance
     */
    public function testAccelerationInit()
    {
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Acceleration,'Zend_Measure_Acceleration Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAccelerationUnknownType()
    {
        try {
            $value = new Zend_Measure_Acceleration('100','Acceleration::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAccelerationUnknownValue()
    {
        try {
            $value = new Zend_Measure_Acceleration('novalue',Zend_Measure_Acceleration::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testAccelerationUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testAccelerationNoLocale()
    {
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Acceleration value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testAccelerationValuePositive()
    {
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testAccelerationValueNegative()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testAccelerationValueDecimal()
    {
        $value = new Zend_Measure_Acceleration('-100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testAccelerationValueDecimalSeperated()
    {
        $value = new Zend_Measure_Acceleration('-100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testAccelerationValueString()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testAccelerationEquality()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $newvalue = new Zend_Measure_Acceleration('otherstring -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Acceleration Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testAccelerationNoEquality()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $newvalue = new Zend_Measure_Acceleration('otherstring -100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Acceleration Object should be not equal');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testAccelerationSerialize()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Measure_Acceleration not serialized');
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testAccelerationUnSerialize()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $serial = $value->serialize();
        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Acceleration not unserialized');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testAccelerationSetPositive()
    {
        $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testAccelerationSetNegative()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testAccelerationSetDecimal()
    {
        $value = new Zend_Measure_Acceleration('-100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testAccelerationSetDecimalSeperated()
    {
        $value = new Zend_Measure_Acceleration('-100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testAccelerationSetString()
    {
        $value = new Zend_Measure_Acceleration('string -100.100,200',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Acceleration Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAccelerationSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Acceleration::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAccelerationSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Acceleration::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAccelerationSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Acceleration('100',Zend_Measure_Acceleration::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Acceleration::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAccelerationSetWithNoLocale()
    {
        $value = new Zend_Measure_Acceleration('100', Zend_Measure_Acceleration::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Acceleration::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Acceleration value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testAccelerationSetType()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $value->setType(Zend_Measure_Acceleration::GRAV);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::GRAV, 'Zend_Measure_Acceleration type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testAccelerationSetComputedType1()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::MILE_PER_HOUR_MINUTE,'de');
        $value->setType(Zend_Measure_Acceleration::GRAV);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::GRAV, 'Zend_Measure_Acceleration type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testAccelerationSetComputedType2()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::GRAV,'de');
        $value->setType(Zend_Measure_Acceleration::MILE_PER_HOUR_MINUTE);
        $this->assertEquals($value->getType(), Zend_Measure_Acceleration::MILE_PER_HOUR_MINUTE, 'Zend_Measure_Acceleration type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testAccelerationSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
            $value->setType('Acceleration::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testAccelerationToString()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals($value->toString(), '-100 m/s²', 'Value -100 m/s² expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testAcceleration_ToString()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $this->assertEquals($value->__toString(), '-100 m/s²', 'Value -100 m/s² expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testAccelerationConversionList()
    {
        $value = new Zend_Measure_Acceleration('-100',Zend_Measure_Acceleration::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
