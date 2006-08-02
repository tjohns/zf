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
        $value = new Zend_Measure_Capacity('100',Zend_Measure_Capacity::FARAD,'de');
        $this->assertEquals($value instanceof Zend_Measure_Capacity,'Zend_Measure_Capacity Object not returned');
    }


    /**
     * test for exception unknown value
     * expected array
     */
/*    public function testCapacityUnknownValue()
    {
        try {
            $value = new Zend_Measure_Capacity('',Zend_Measure_Capacity::FARAD,'de');
            $this->assertTrue($value,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }
*/}
