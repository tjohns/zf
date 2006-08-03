<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Capacity
 */
require_once 'Zend/Measure/Capacity.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_CapacityTest extends PHPUnit2_Framework_TestCase
{

    public function setUp()
    {
    }
    
    
    /**
     * test for capacity initialisation
     * expected array
     */
    public function testCapacityInit()
    {
        $value = new Zend_Measure_Capacity('100',Zend_Measure_Capacity::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Capacity,'Zend_Measure_Capacity Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCapacityUnknownType()
    {
        try {
            $value = new Zend_Measure_Capacity('100','Capacity::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
/*    public function testCapacityUnknownValue()
    {
        try {
            $value = new Zend_Measure_Capacity('novalue',Zend_Measure_Capacity::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }
*/

    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testCapacityUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Capacity('100',Zend_Measure_Capacity::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for positive value
     * expected array
     */
    public function testCapacityValuePositive()
    {
        $value = new Zend_Measure_Capacity('100',Zend_Measure_Capacity::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Capacity value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected array
     */
    public function testCapacityValueNegative()
    {
        $value = new Zend_Measure_Capacity('-100',Zend_Measure_Capacity::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Capacity value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected array
     */
    public function testCapacityValueDecimal()
    {
        $value = new Zend_Measure_Capacity('-100,200',Zend_Measure_Capacity::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Capacity value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected array
     */
    public function testCapacityValueDecimalSeperated()
    {
        $value = new Zend_Measure_Capacity('-100.100,200',Zend_Measure_Capacity::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Capacity Object not returned');
    }


    /**
     * test for string with integrated value
     * expected array
     */
    public function testCapacityValueString()
    {
        $value = new Zend_Measure_Capacity('string -100.100,200',Zend_Measure_Capacity::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Capacity Object not returned');
    }
}
