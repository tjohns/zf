<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure
 */
require_once 'Zend.php';
Zend::loadClass('Zend_Measure');
Zend::loadClass('Zend_Measure_Temperature');

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_MeasureTest extends PHPUnit_Framework_TestCase
{


    /**
     * test for Angle initialisation
     * expected instance
     */
    public function testMeasureInit()
    {
        $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'de');
        $this->assertTrue($value instanceof Zend_Measure,'Zend_Measure Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testMeasureUnknownType()
    {
        try {
            $value = new Zend_Measure('100','Zend_Measure::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testMeasureUnknownValue()
    {
        try {
            $value = new Zend_Measure('novalue',Zend_Measure::TEMPERATURE,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testMeasureUnknownLocale()
    {
        try {
            $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for standard locale
     * expected root value
     */
    public function testMeasureStandardLocale()
    {
        $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE);
        $this->assertTrue(is_object($value),'Object expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testMeasureValuePositive()
    {
        $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testMeasureValueNegative()
    {
        $value = new Zend_Measure('-100',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testMeasureValueDecimal()
    {
        $value = new Zend_Measure('-100,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testMeasureValueDecimalSeperated()
    {
        $value = new Zend_Measure('-100.100,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testMeasureValueString()
    {
        $value = new Zend_Measure('string -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testMeasureEquality()
    {
        $value = new Zend_Measure('string -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $newvalue = new Zend_Measure('otherstring -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testMeasureNoEquality()
    {
        $value = new Zend_Measure('string -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $newvalue = new Zend_Measure('otherstring -100,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure Object should be not equal');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testMeasureSerialize()
    {
        $value = new Zend_Measure('string -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Measure not serialized');
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testMeasureUnSerialize()
    {
        $value = new Zend_Measure('string -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $serial = $value->serialize();
        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Measure not unserialized');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testMeasureSetPositive()
    {
        $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'de');
        $value->setValue('200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testMeasureSetNegative()
    {
        $value = new Zend_Measure('-100',Zend_Measure::TEMPERATURE,'de');
        $value->setValue('-200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testMeasureSetDecimal()
    {
        $value = new Zend_Measure('-100,200',Zend_Measure::TEMPERATURE,'de');
        $value->setValue('-200,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testMeasureSetDecimalSeperated()
    {
        $value = new Zend_Measure('-100.100,200',Zend_Measure::TEMPERATURE,'de');
        $value->setValue('-200.200,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testMeasureSetString()
    {
        $value = new Zend_Measure('string -100.100,200',Zend_Measure::TEMPERATURE,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testMeasureSetUnknownType()
    {
        try {
            $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'de');
            $value->setValue('otherstring -200.200,200','Temperature::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testMeasureSetUnknownValue()
    {
        try {
            $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'de');
            $value->setValue('novalue',Zend_Measure::TEMPERATURE,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testMeasureSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure('100',Zend_Measure::TEMPERATURE,'de');
            $value->setValue('200',Zend_Measure::TEMPERATURE,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testMeasureSetWithNoLocale()
    {
        $value = new Zend_Measure('100', Zend_Measure::TEMPERATURE, 'de');
        $value->setValue('200', Zend_Measure::TEMPERATURE);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testMeasureSetType()
    {
        $value = new Zend_Measure('-100',Zend_Measure::TEMPERATURE,'de');
        $value->setType(Zend_Measure_Temperature::KELVIN);
        $this->assertEquals($value->getType(), Zend_Measure_Temperature::KELVIN, 'Zend_Measure_Temperature type expected');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testMeasureSetTypeAlternate()
    {
        $value = new Zend_Measure('-100',Zend_Measure_Temperature::CELSIUS,'de');
        $value->setType(Zend_Measure::TEMPERATURE);
        $this->assertEquals($value->getType(), 'Temperature::KELVIN', 'Zend_Measure type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testMeasureSetTypeFailed()
    {
        try {
            $value = new Zend_Measure('-100',Zend_Measure::TEMPERATURE,'de');
            $value->setType('Temperature::UNKNOWN');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // OK
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testMeasureToString()
    {
        $value = new Zend_Measure('-100',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals($value->toString(), '-100 °K', 'Value -100 °K expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testMeasure_ToString()
    {
        $value = new Zend_Measure('-100',Zend_Measure::TEMPERATURE,'de');
        $this->assertEquals($value->__toString(), '-100 °K', 'Value -100 °K expected');
    }


    /**
     * test setting type
     * expected string
     */
    public function testMeasureConvertTo()
    {
        $value = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $this->assertEquals($value->convertTo(Zend_Measure_Temperature::CELSIUS), '-174.15 °C', 'Value -174.15 °C expected');
    }


    /**
     * test setting type
     * expected exception
     */
    public function testMeasureConvertToFailed()
    {
        try {
            $value = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
            $newvalue = $value->convertTo('Temperature::UNKNOWN');
            $this->assertTrue(false, 'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test adding object
     * expected new type
     */
    public function testMeasureAdd()
    {
        $value  = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $value2 = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $newvalue = $value->add($value2);
        $this->assertEquals($newvalue->toString(), '200 °K', 'Value 200 °K expected');
    }


    /**
     * test adding object
     * expected exception
     */
    public function testMeasureAddFailed()
    {
        try {
            $value  = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
            $value2 = new Zend_Measure('100',Zend_Measure::LENGTH,'de');
            $newvalue = $value->add($value2);
            $this->assertTrue(false, 'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test substract object
     * expected new type
     */
    public function testMeasureSub()
    {
        $value  = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $value2 = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $newvalue = $value->sub($value2);
        $this->assertEquals($newvalue->toString(), '0 °K', 'Value 0 °K expected');
    }


    /**
     * test substract object
     * expected exception
     */
    public function testMeasureSubFailed()
    {
        try {
            $value  = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
            $value2 = new Zend_Measure('100',Zend_Measure::LENGTH,'de');
            $newvalue = $value->sub($value2);
            $this->assertTrue(false, 'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test compare object
     * expected integer
     */
    public function testMeasureCompare()
    {
        $value  = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $value2 = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $newvalue = $value->compare($value2);
        $this->assertEquals($newvalue, '0', 'Value 0 expected');
    }


    /**
     * test compare object
     * expected integer
     */
    public function testMeasureCompareGreater()
    {
        $value  = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $value2 = new Zend_Measure('50',Zend_Measure_Temperature::KELVIN,'de');
        $newvalue = $value->compare($value2);
        $this->assertEquals($newvalue, '50', 'Value 50 expected');
    }


    /**
     * test compare object
     * expected integer
     */
    public function testMeasureCompareSmaller()
    {
        $value  = new Zend_Measure('50',Zend_Measure_Temperature::KELVIN,'de');
        $value2 = new Zend_Measure('100',Zend_Measure_Temperature::KELVIN,'de');
        $newvalue = $value->compare($value2);
        $this->assertEquals($newvalue, '-50', 'Value -50 expected');
    }


    /**
     * test list of unittypes
     * expected array
     */
    public function testMeasureAllTypes()
    {
        $value = new Zend_Measure('50',Zend_Measure_Temperature::KELVIN,'de');
        $unit  = $value->getAllTypes();
        $this->assertTrue(is_array($unit), 'Array expected');
    }


    /**
     * test list of all types
     * expected array
     */
    public function testMeasureTypeList()
    {
        $value = new Zend_Measure('50',Zend_Measure_Temperature::KELVIN,'de');
        $unit  = $value->getTypeList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
