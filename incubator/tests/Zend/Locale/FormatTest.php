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
    /**
     * test standard locale
     * expected integer
     */
    public function testStandardNull()
    {
        $value = Zend_Locale_Format::getNumber(0);
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative integer standard locale
     * expected integer
     */
    public function testStandardNegativeInt()
    {
        $value = Zend_Locale_Format::getNumber(-1234567);
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive integer standard locale
     * expected integer
     */
    public function testStandardPositiveInt()
    {
        $value = Zend_Locale_Format::getNumber(1234567);
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test standard locale with seperation
     * expected integer
     */
    public function testStandardSeperatedNull()
    {
        $value = Zend_Locale_Format::getNumber(0.1234567);
        $this->assertEquals($value, 0.1234567, "value 0.1234567 expected");
    }


    /**
     * test negative seperation standard locale
     * expected integer
     */
    public function testStandardSeperatedNegative()
    {
        $value = Zend_Locale_Format::getNumber(-1234567.12345);
        $this->assertEquals($value, -1234567.12345, "value -1234567.12345 expected");
    }


    /**
     * test positive seperation standard locale
     * expected integer
     */
    public function testStandardSeperatedPositive()
    {
        $value = Zend_Locale_Format::getNumber(1234567.12345);
        $this->assertEquals($value, 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test language locale
     * expected integer
     */
    public function testLanguageNull()
    {
        $value = Zend_Locale_Format::getNumber('0', 'de');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative integer language locale
     * expected integer
     */
    public function testLanguageNegativeInt()
    {
        $value = Zend_Locale_Format::getNumber('-1234567', 'de');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive integer language locale
     * expected integer
     */
    public function testLanguagePositiveInt()
    {
        $value = Zend_Locale_Format::getNumber('1234567', 'de');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test language locale with seperation
     * expected integer
     */
    public function testLanguageSeperatedNull()
    {
        $value = Zend_Locale_Format::getNumber('0,1234567', 'de');
        $this->assertEquals($value, 0.1234567, "value 0.1234567 expected");
    }


    /**
     * test negative seperation language locale
     * expected integer
     */
    public function testLanguageSeperatedNegative()
    {
        $value = Zend_Locale_Format::getNumber('-1.234.567,12345', 'de');
        $this->assertEquals($value, -1234567.12345, "value -1234567.12345 expected");
    }


    /**
     * test positive seperation language locale
     * expected integer
     */
    public function testLanguageSeperatedPositive()
    {
        $value = Zend_Locale_Format::getNumber('1.234.567,12345', 'de');
        $this->assertEquals($value, 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test region locale
     * expected integer
     */
    public function testRegionNull()
    {
        $value = Zend_Locale_Format::getNumber('0', 'de_AT');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative integer region locale
     * expected integer
     */
    public function testRegionNegativeInt()
    {
        $value = Zend_Locale_Format::getNumber('-1234567', 'de_AT');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive integer region locale
     * expected integer
     */
    public function testRegionPositiveInt()
    {
        $value = Zend_Locale_Format::getNumber('1.234.567', 'de_AT');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test region locale with seperation
     * expected integer
     */
    public function testRegionSeperatedNull()
    {
        $value = Zend_Locale_Format::getNumber('0,1234567', 'de_AT');
        $this->assertEquals($value, 0.1234567, "value 0.1234567 expected");
    }


    /**
     * test negative seperation region locale
     * expected integer
     */
    public function testRegionSeperatedNegative()
    {
        $value = Zend_Locale_Format::getNumber('-1.234.567,12345', 'de_AT');
        $this->assertEquals($value, -1234567.12345, "value -1234567.12345 expected");
    }


    /**
     * test positive seperation language locale
     * expected integer
     */
    public function testRegionSeperatedPositive()
    {
        $value = Zend_Locale_Format::getNumber('1.234.567,12345', 'de_AT');
        $this->assertEquals($value, 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test to standard locale
     * expected string
     */
    public function testStandardToNull()
    {
        $value = Zend_Locale_Format::toNumber(0);
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test to language locale
     * expected string
     */
    public function testLanguageToNull()
    {
        $value = Zend_Locale_Format::toNumber(0, 'de');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test to region locale
     * expected string
     */
    public function testRegionToNull()
    {
        $value = Zend_Locale_Format::toNumber(0, 'de_AT');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test negative integer region locale
     * expected string
     */
    public function testRegionToNegativeInt()
    {
        $value = Zend_Locale_Format::toNumber(-1234567, 'de_AT');
        $this->assertEquals($value, '-1.234.567', "string -1.234.567 expected");
    }


    /**
     * test positive integer region locale
     * expected string
     */
    public function testRegionToPositiveInt()
    {
        $value = Zend_Locale_Format::toNumber(1234567, 'de_AT');
        $this->assertEquals($value, '1.234.567', "string 1.234.567 expected");
    }


    /**
     * test region locale with seperation
     * expected string
     */
    public function testRegionToSeperatedNull()
    {
        $value = Zend_Locale_Format::toNumber(0.1234567, 'de_AT');
        $this->assertEquals($value, '0,1234567', "string 0,1234567 expected");
    }


    /**
     * test negative seperation region locale
     * expected string
     */
    public function testRegionToSeperatedNegative()
    {
        $value = Zend_Locale_Format::toNumber(-1234567.12345, 'de_AT');
        $this->assertEquals($value, '-1.234.567,12345', "string -1.234.567,12345 expected");
    }


    /**
     * test positive seperation language locale
     * expected string
     */
    public function testRegionToSeperatedPositive()
    {
        $value = Zend_Locale_Format::toNumber(1234567.12345, 'de_AT');
        $this->assertEquals($value, '1.234.567,12345', "value 1.234.567,12345 expected");
    }


    /**
     * test without seperation language locale
     * expected string
     */
    public function testRegionWithoutSeperatedPositive()
    {
        $value = Zend_Locale_Format::toNumber(1234567.12345, 'ar_QA');
        $this->assertEquals($value, '1234567٫12345', "value 1234567٫12345 expected");
    }


    /**
     * test without seperation language locale
     * expected string
     */
    public function testRegionWithoutSeperatedNegative()
    {
        $value = Zend_Locale_Format::toNumber(-1234567.12345, 'ar_QA');
        $this->assertEquals($value, '1234567٫12345-', "value 1234567٫12345- expected");
    }


    /**
     * test with two seperation language locale
     * expected string
     */
    public function testRegionWithTwoSeperatedPositive()
    {
        $value = Zend_Locale_Format::toNumber(1234567.12345, 'dz_BT');
        $this->assertEquals($value, '12,34,567.12345', "value 12,34,567.12345 expected");
    }


    /**
     * test with two seperation language locale
     * expected string
     */
    public function testRegionWithTwoSeperatedNegative()
    {
        $value = Zend_Locale_Format::toNumber(-1234567.12345, 'mk_MK');
        $this->assertEquals($value, '-(1.234.567,12345)', "value -(1.234.567,12345) expected");
    }


    /**
     * test if isNumber
     * expected boolean
     */
    public function testIsNumber()
    {
        $value = Zend_Locale_Format::isNumber('-1.234.567,12345', 'de_AT');
        $this->assertEquals($value, TRUE, "TRUE expected");
    }


    /**
     * test if isNumberFailed
     * expected boolean
     */
    public function testIsNumberFailed()
    {
        $value = Zend_Locale_Format::isNumber('textwithoutnumber', 'de_AT');
        $this->assertEquals($value, FALSE, "FALSE expected");
    }


    /**
     * test float novalue standard locale
     * expected exception
     */
    public function testFloatNoValueStandardNull()
    {
        try {
            $value = Zend_Locale_Format::getFloat('nocontent');
            $this->assertTrue(false, "exception expected");
        } catch (Exception $e) {
            return true;
        }
    }


    /**
     * test float standard locale
     * expected integer
     */
    public function testFloatStandardNull()
    {
        $value = Zend_Locale_Format::getFloat(0);
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative float standard locale
     * expected integer
     */
    public function testStandardNegativeFloat()
    {
        $value = Zend_Locale_Format::getFloat(-1234567);
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive float standard locale
     * expected integer
     */
    public function testStandardPositiveFloat()
    {
        $value = Zend_Locale_Format::getFloat(1234567);
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test standard locale with float seperation
     * expected integer
     */
    public function testStandardSeperatedNullFloat()
    {
        $value = Zend_Locale_Format::getFloat(0.1234567);
        $this->assertEquals($value, 0.1234567, "value 0.1234567 expected");
    }


    /**
     * test negative float seperation standard locale
     * expected integer
     */
    public function testStandardSeperatedNegativeFloat()
    {
        $value = Zend_Locale_Format::getFloat(-1234567.12345);
        $this->assertEquals($value, -1234567.12345, "value -1234567.12345 expected");
    }


    /**
     * test positive float seperation standard locale
     * expected integer
     */
    public function testStandardSeperatedPositiveFloat()
    {
        $value = Zend_Locale_Format::getFloat(1234567.12345);
        $this->assertEquals($value, 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test language locale float
     * expected integer
     */
    public function testLanguageNullFloat()
    {
        $value = Zend_Locale_Format::getFloat('0', 'de');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative float language locale
     * expected integer
     */
    public function testLanguageNegativeFloat()
    {
        $value = Zend_Locale_Format::getFloat('-1234567', 'de');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive float language locale
     * expected integer
     */
    public function testLanguagePositiveFloat()
    {
        $value = Zend_Locale_Format::getFloat('1234567', 'de');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test language locale with seperation
     * expected integer
     */
    public function testLanguageSeperatedNullFloat()
    {
        $value = Zend_Locale_Format::getFloat('0,1234567', 'de');
        $this->assertEquals($value, 0.1234567, "value 0.1234567 expected");
    }


    /**
     * test negative float seperation language locale
     * expected integer
     */
    public function testLanguageSeperatedNegativeFloat()
    {
        $value = Zend_Locale_Format::getFloat('-1.234.567,12345', 'de');
        $this->assertEquals($value, -1234567.12345, "value -1234567.12345 expected");
    }


    /**
     * test positive float seperation language locale
     * expected integer
     */
    public function testLanguageSeperatedPositiveFloat()
    {
        $value = Zend_Locale_Format::getFloat('1.234.567,12345', 'de');
        $this->assertEquals($value, 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test region locale float
     * expected integer
     */
    public function testRegionNullFloat()
    {
        $value = Zend_Locale_Format::getFloat('0', 'de_AT');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative float region locale
     * expected integer
     */
    public function testRegionNegativeFloat()
    {
        $value = Zend_Locale_Format::getFloat('-1234567', 'de_AT');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive float region locale
     * expected integer
     */
    public function testRegionPositiveFloat()
    {
        $value = Zend_Locale_Format::getFloat('1.234.567', 'de_AT');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test region locale with float seperation
     * expected integer
     */
    public function testRegionSeperatedNullFloat()
    {
        $value = Zend_Locale_Format::getFloat('0,1234567', 'de_AT');
        $this->assertEquals($value, 0.1234567, "value 0.1234567 expected");
    }


    /**
     * test negative float seperation region locale
     * expected integer
     */
    public function testRegionSeperatedNegativeFloat()
    {
        $value = Zend_Locale_Format::getFloat('-1.234.567,12345', 'de_AT');
        $this->assertEquals($value, -1234567.12345, "value -1234567.12345 expected");
    }


    /**
     * test positive float seperation language locale
     * expected integer
     */
    public function testRegionSeperatedPositiveFloat()
    {
        $value = Zend_Locale_Format::getFloat('1.234.567,12345', 'de_AT');
        $this->assertEquals($value, 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test region locale float precision
     * expected integer
     */
    public function testRegionNullFloatPrec()
    {
        $value = Zend_Locale_Format::getFloat('0', 2, 'de_AT');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative float region locale precision
     * expected integer
     */
    public function testRegionNegativeFloatPrec()
    {
        $value = Zend_Locale_Format::getFloat('-1234567', 2, 'de_AT');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive float region locale precision
     * expected integer
     */
    public function testRegionPositiveFloatPrec()
    {
        $value = Zend_Locale_Format::getFloat('1.234.567', 2, 'de_AT');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test region locale with float seperation precision
     * expected integer
     */
    public function testRegionSeperatedNullFloatPrec()
    {
        $value = Zend_Locale_Format::getFloat('0,1234567', 2, 'de_AT');
        $this->assertEquals($value, 0.12, "value 0.12 expected");
    }


    /**
     * test negative float seperation region locale precision
     * expected integer
     */
    public function testRegionSeperatedNegativeFloatPrec()
    {
        $value = Zend_Locale_Format::getFloat('-1.234.567,12345', 2, 'de_AT');
        $this->assertEquals($value, -1234567.12, "value -1234567.12 expected");
    }


    /**
     * test positive float seperation language locale precision
     * expected integer
     */
    public function testRegionSeperatedPositiveFloatPrec()
    {
        $value = Zend_Locale_Format::getFloat('1.234.567,12345', 2, 'de_AT');
        $this->assertEquals($value, 1234567.12, "value 1234567.12 expected");
    }


    /**
     * test positive float seperation language locale precision
     * expected integer
     */
    public function testRegionSeperatedPositiveFloatPrecAdd()
    {
        $value = Zend_Locale_Format::getFloat('1.234.567,12345', 7, 'de_AT');
        $this->assertEquals($value, '1234567.12345', "value 1234567.12345 expected");
    }
}
