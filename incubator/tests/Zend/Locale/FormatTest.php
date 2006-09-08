<?php
/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */


/**
 * Zend_Locale_Format
 */
require_once 'Zend/Locale/Format.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Locale
 * @subpackage UnitTests
 */
class Zend_Locale_FormatTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }
    
    
    /**
     * test for positive string in fa
     * expected integer
     */
    public function testGetNumberNegativeFa()
    {
        $value = Zend_Locale_Format::getNumber('123','fa');
        $this->assertEquals($value,123);
    }


    /**
     * test for positive string in fa
     * expected integer
     */
/*    public function testGetNumberException()
    {
        try {
            $value = Zend_Locale_Format::getNumber('','fa');
            $this->fail('exception expected for string without containing number');
        } catch (Exception $e) {
            return; // Test OK
        }
    }
*/

    /**
     * test for decimal string in de
     * expected exception
     */
/*    public function testGetNumberDecimal()
    {
        $value = Zend_Locale_Format::getNumber('-123.456','de');
        $this->assertEquals(-123.456,$value);
    }
*/
}
