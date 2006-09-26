<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Weight
 */
require_once 'Zend/Measure/Weight.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_WeightTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }


    /**
     * test for Weight initialisation
     * expected instance
     */
    public function testWeightInit()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Weight,'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testWeightUnknownType()
    {
        try {
            $value = new Zend_Measure_Weight('100','Weight::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testWeightUnknownValue()
    {
        try {
            $value = new Zend_Measure_Weight('novalue',Zend_Measure_Weight::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testWeightUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testWeightNoLocale()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Weight value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testWeightValuePositive()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Weight value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testWeightValueNegative()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Weight value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testWeightValueDecimal()
    {
        $value = new Zend_Measure_Weight('-100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Weight value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testWeightValueDecimalSeperated()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testWeightValueString()
    {
        $value = new Zend_Measure_Weight('string -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testWeightEquality()
    {
        $value = new Zend_Measure_Weight('string -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $newvalue = new Zend_Measure_Weight('otherstring -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Weight Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testWeightNoEquality()
    {
        $value = new Zend_Measure_Weight('string -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $newvalue = new Zend_Measure_Weight('otherstring -100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Weight Object should be not equal');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testWeightSerialize()
    {
        $value = new Zend_Measure_Weight('string -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Measure_Weight not serialized');
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testWeightUnSerialize()
    {
        $value = new Zend_Measure_Weight('string -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $serial = $value->serialize();
        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Weight not unserialized');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testWeightSetPositive()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Weight value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testWeightSetNegative()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Weight value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testWeightSetDecimal()
    {
        $value = new Zend_Measure_Weight('-100,200',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Weight value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testWeightSetDecimalSeperated()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testWeightSetString()
    {
        $value = new Zend_Measure_Weight('string -100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testWeightSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Weight::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testWeightSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Weight::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testWeightSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Weight::STANDARD,'nolocale');
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
        $value = new Zend_Measure_Weight('100', Zend_Measure_Weight::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Weight::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Weight value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testWeightSetType()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $value->setType(Zend_Measure_Weight::GRAM);
        $this->assertEquals($value->getType(), Zend_Measure_Weight::GRAM, 'Zend_Measure_Weight type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testWeightSetComputedType1()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::DRAM,'de');
        $value->setType(Zend_Measure_Weight::OUNCE);
        $this->assertEquals($value->getType(), Zend_Measure_Weight::OUNCE, 'Zend_Measure_Weight type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testWeightSetComputedType2()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::OUNCE,'de');
        $value->setType(Zend_Measure_Weight::DRAM);
        $this->assertEquals($value->getType(), Zend_Measure_Weight::DRAM, 'Zend_Measure_Weight type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testWeightSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
            $value->setType('Weight::UNKNOWN');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // OK
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testWeightToString()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals($value->toString(), '-100 kg', 'Value -100 kg expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testWeight_ToString()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals($value->__toString(), '-100 kg', 'Value -100 kg expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testWeightConversionList()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
