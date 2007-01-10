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
 * @package    Zend_Format
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
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
     * test with two seperation language locale
     * expected string
     */
    public function testRegionWithNoSeperation()
    {
        $value = Zend_Locale_Format::toNumber(452.25, 'en_US');
        $this->assertEquals($value, '452.25', "value 452.25 expected");
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
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
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



    /**
     * test to standard locale
     * expected string
     */
    public function testFloatToNull()
    {
        $value = Zend_Locale_Format::toFloat(0);
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test to language locale
     * expected string
     */
    public function testFloatLanguageToNull()
    {
        $value = Zend_Locale_Format::toFloat(0, 'de');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test to region locale
     * expected string
     */
    public function testFloatRegionToNull()
    {
        $value = Zend_Locale_Format::toFloat(0, 'de_AT');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test negative integer region locale
     * expected string
     */
    public function testFloatRegionToNegativeInt()
    {
        $value = Zend_Locale_Format::toFloat(-1234567, 'de_AT');
        $this->assertEquals($value, '-1.234.567', "string -1.234.567 expected");
    }


    /**
     * test positive integer region locale
     * expected string
     */
    public function testFloatRegionToPositiveInt()
    {
        $value = Zend_Locale_Format::toFloat(1234567, 'de_AT');
        $this->assertEquals($value, '1.234.567', "string 1.234.567 expected");
    }


    /**
     * test region locale with seperation
     * expected string
     */
    public function testFloatRegionToSeperatedNull()
    {
        $value = Zend_Locale_Format::toFloat(0.1234567, 'de_AT');
        $this->assertEquals($value, '0,1234567', "string 0,1234567 expected");
    }


    /**
     * test negative seperation region locale
     * expected string
     */
    public function testFloatRegionToSeperatedNegative()
    {
        $value = Zend_Locale_Format::toFloat(-1234567.12345, 'de_AT');
        $this->assertEquals($value, '-1.234.567,12345', "string -1.234.567,12345 expected");
    }


    /**
     * test positive seperation language locale
     * expected string
     */
    public function testFloatRegionToSeperatedPositive()
    {
        $value = Zend_Locale_Format::toFloat(1234567.12345, 'de_AT');
        $this->assertEquals($value, '1.234.567,12345', "value 1.234.567,12345 expected");
    }


    /**
     * test without seperation language locale
     * expected string
     */
    public function testFloatRegionWithoutSeperatedPositive()
    {
        $value = Zend_Locale_Format::toFloat(1234567.12345, 'ar_QA');
        $this->assertEquals($value, '1234567٫12345', "value 1234567٫12345 expected");
    }


    /**
     * test without seperation language locale
     * expected string
     */
    public function testFloatRegionWithoutSeperatedNegative()
    {
        $value = Zend_Locale_Format::toFloat(-1234567.12345, 'ar_QA');
        $this->assertEquals($value, '1234567٫12345-', "value 1234567٫12345- expected");
    }


    /**
     * test with two seperation language locale
     * expected string
     */
    public function testFloatRegionWithTwoSeperatedPositive()
    {
        $value = Zend_Locale_Format::toFloat(1234567.12345, 'dz_BT');
        $this->assertEquals($value, '12,34,567.12345', "value 12,34,567.12345 expected");
    }


    /**
     * test with two seperation language locale
     * expected string
     */
    public function testFloatRegionWithTwoSeperatedNegative()
    {
        $value = Zend_Locale_Format::toFloat(-1234567.12345, 'mk_MK');
        $this->assertEquals($value, '-(1.234.567,12345)', "value -(1.234.567,12345) expected");
    }


    /**
     * test region locale float precision
     * expected integer
     */
    public function testFloatRegionNullFloatPrec()
    {
        $value = Zend_Locale_Format::toFloat(0, 2, 'de_AT');
        $this->assertEquals($value, '0', "value 0 expected");
    }


    /**
     * test negative float region locale precision
     * expected integer
     */
    public function testFloatRegionNegativeFloatPrec()
    {
        $value = Zend_Locale_Format::toFloat(-1234567, 2, 'de_AT');
        $this->assertEquals($value, '-1.234.567', "value -1.234.567 expected");
    }


    /**
     * test positive float region locale precision
     * expected integer
     */
    public function testFloatRegionPositiveFloatPrec()
    {
        $value = Zend_Locale_Format::toFloat(1234567, 2, 'de_AT');
        $this->assertEquals($value, '1.234.567', "value 1.234.567 expected");
    }


    /**
     * test region locale with float seperation precision
     * expected integer
     */
    public function testFloatRegionSeperatedNullFloatPrec()
    {
        $value = Zend_Locale_Format::toFloat(0.1234567, 2, 'de_AT');
        $this->assertEquals($value, '0,12', "value 0,12 expected");
    }


    /**
     * test negative float seperation region locale precision
     * expected integer
     */
    public function testFloatRegionSeperatedNegativeFloatPrec()
    {
        $value = Zend_Locale_Format::toFloat(-1234567.12345, 2, 'de_AT');
        $this->assertEquals($value, '-1.234.567,12', "value -1.234.567,12 expected");
    }


    /**
     * test positive float seperation language locale precision
     * expected integer
     */
    public function testFloatRegionSeperatedPositiveFloatPrec()
    {
        $value = Zend_Locale_Format::toFloat(1234567.12345, 2, 'de_AT');
        $this->assertEquals($value, '1.234.567,12', "value 1.234.567,12 expected");
    }


    /**
     * test positive float seperation language locale precision
     * expected integer
     */
    public function testFloatRegionSeperatedPositiveFloatPrecAdd()
    {
        $value = Zend_Locale_Format::toFloat(1234567.12345, 7, 'de_AT');
        $this->assertEquals($value, '1.234.567,12345', "value 1.234.567,12345 expected");
    }

    
    /**
     * test if isNumber
     * expected boolean
     */
    public function testIsFloat()
    {
        $value = Zend_Locale_Format::isFloat('-1.234.567,12345', 'de_AT');
        $this->assertEquals($value, TRUE, "TRUE expected");
    }


    /**
     * test if isNumberFailed
     * expected boolean
     */
    public function testIsFloatFailed()
    {
        $value = Zend_Locale_Format::isFloat('textwithoutnumber', 'de_AT');
        $this->assertEquals($value, FALSE, "FALSE expected");
    }


    /**
     * test standard locale
     * expected integer
     */
    public function testIntegerNull()
    {
        $value = Zend_Locale_Format::getInteger(0);
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative integer standard locale
     * expected integer
     */
    public function testIntegerNegativeInt()
    {
        $value = Zend_Locale_Format::getInteger(-1234567);
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive integer standard locale
     * expected integer
     */
    public function testIntegerPositiveInt()
    {
        $value = Zend_Locale_Format::getInteger(1234567);
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test standard locale with seperation
     * expected integer
     */
    public function testIntegerSeperatedNull()
    {
        $value = Zend_Locale_Format::getInteger(0.1234567);
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative seperation standard locale
     * expected integer
     */
    public function testIntegerSeperatedNegative()
    {
        $value = Zend_Locale_Format::getInteger(-1234567.12345);
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive seperation standard locale
     * expected integer
     */
    public function testIntegerSeperatedPositive()
    {
        $value = Zend_Locale_Format::getInteger(1234567.12345);
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test language locale
     * expected integer
     */
    public function testIntegerLanguageNull()
    {
        $value = Zend_Locale_Format::getInteger('0', 'de');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative integer language locale
     * expected integer
     */
    public function testIntegerLanguageNegativeInt()
    {
        $value = Zend_Locale_Format::getInteger('-1234567', 'de');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive integer language locale
     * expected integer
     */
    public function testIntegerLanguagePositiveInt()
    {
        $value = Zend_Locale_Format::getInteger('1234567', 'de');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test language locale with seperation
     * expected integer
     */
    public function testIntegerLanguageSeperatedNull()
    {
        $value = Zend_Locale_Format::getInteger('0,1234567', 'de');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative seperation language locale
     * expected integer
     */
    public function testIntegerLanguageSeperatedNegative()
    {
        $value = Zend_Locale_Format::getInteger('-1.234.567,12345', 'de');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive seperation language locale
     * expected integer
     */
    public function testIntegerLanguageSeperatedPositive()
    {
        $value = Zend_Locale_Format::getInteger('1.234.567,12345', 'de');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test region locale
     * expected integer
     */
    public function testIntegerRegionNull()
    {
        $value = Zend_Locale_Format::getInteger('0', 'de_AT');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative integer region locale
     * expected integer
     */
    public function testIntegerRegionNegativeInt()
    {
        $value = Zend_Locale_Format::getInteger('-1234567', 'de_AT');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive integer region locale
     * expected integer
     */
    public function testIntegerRegionPositiveInt()
    {
        $value = Zend_Locale_Format::getInteger('1.234.567', 'de_AT');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test region locale with seperation
     * expected integer
     */
    public function testIntegerRegionSeperatedNull()
    {
        $value = Zend_Locale_Format::getInteger('0,1234567', 'de_AT');
        $this->assertEquals($value, 0, "value 0 expected");
    }


    /**
     * test negative seperation region locale
     * expected integer
     */
    public function testIntegerRegionSeperatedNegative()
    {
        $value = Zend_Locale_Format::getInteger('-1.234.567,12345', 'de_AT');
        $this->assertEquals($value, -1234567, "value -1234567 expected");
    }


    /**
     * test positive seperation language locale
     * expected integer
     */
    public function testIntegerRegionSeperatedPositive()
    {
        $value = Zend_Locale_Format::getInteger('1.234.567,12345', 'de_AT');
        $this->assertEquals($value, 1234567, "value 1234567 expected");
    }


    /**
     * test to standard locale
     * expected string
     */
    public function testIntegerStandardToNull()
    {
        $value = Zend_Locale_Format::toInteger(0);
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test to language locale
     * expected string
     */
    public function testIntegerLanguageToNull()
    {
        $value = Zend_Locale_Format::toInteger(0, 'de');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test to region locale
     * expected string
     */
    public function testIntegerRegionToNull()
    {
        $value = Zend_Locale_Format::toInteger(0, 'de_AT');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test negative integer region locale
     * expected string
     */
    public function testIntegerRegionToNegativeInt()
    {
        $value = Zend_Locale_Format::toInteger(-1234567, 'de_AT');
        $this->assertEquals($value, '-1.234.567', "string -1.234.567 expected");
    }


    /**
     * test positive integer region locale
     * expected string
     */
    public function testIntegerRegionToPositiveInt()
    {
        $value = Zend_Locale_Format::toInteger(1234567, 'de_AT');
        $this->assertEquals($value, '1.234.567', "string 1.234.567 expected");
    }


    /**
     * test region locale with seperation
     * expected string
     */
    public function testIntegerRegionToSeperatedNull()
    {
        $value = Zend_Locale_Format::toInteger(0.1234567, 'de_AT');
        $this->assertEquals($value, '0', "string 0 expected");
    }


    /**
     * test negative seperation region locale
     * expected string
     */
    public function testIntegerRegionToSeperatedNegative()
    {
        $value = Zend_Locale_Format::toInteger(-1234567.12345, 'de_AT');
        $this->assertEquals($value, '-1.234.567', "string -1.234.567 expected");
    }


    /**
     * test positive seperation language locale
     * expected string
     */
    public function testIntegerRegionToSeperatedPositive()
    {
        $value = Zend_Locale_Format::toInteger(1234567.12345, 'de_AT');
        $this->assertEquals($value, '1.234.567', "value 1.234.567 expected");
    }


    /**
     * test without seperation language locale
     * expected string
     */
    public function testIntegerRegionWithoutSeperatedPositive()
    {
        $value = Zend_Locale_Format::toInteger(1234567.12345, 'ar_QA');
        $this->assertEquals($value, '1234567', "value 1234567 expected");
    }


    /**
     * test without seperation language locale
     * expected string
     */
    public function testIntegerRegionWithoutSeperatedNegative()
    {
        $value = Zend_Locale_Format::toInteger(-1234567.12345, 'ar_QA');
        $this->assertEquals($value, '1234567-', "value 1234567- expected");
    }


    /**
     * test with two seperation language locale
     * expected string
     */
    public function testIntegerRegionWithTwoSeperatedPositive()
    {
        $value = Zend_Locale_Format::toInteger(1234567.12345, 'dz_BT');
        $this->assertEquals($value, '12,34,567', "value 12,34,567 expected");
    }


    /**
     * test with two seperation language locale
     * expected string
     */
    public function testIntegerRegionWithTwoSeperatedNegative()
    {
        $value = Zend_Locale_Format::toInteger(-1234567.12345, 'mk_MK');
        $this->assertEquals($value, '-(1.234.567)', "value -(1.234.567) expected");
    }


    /**
     * test if isNumber
     * expected boolean
     */
    public function testIsInteger()
    {
        $value = Zend_Locale_Format::isInteger('-1.234.567,12345', 'de_AT');
        $this->assertEquals($value, TRUE, "TRUE expected");
    }


    /**
     * test if isNumberFailed
     * expected boolean
     */
    public function testIsIntegerFailed()
    {
        $value = Zend_Locale_Format::isInteger('textwithoutnumber', 'de_AT');
        $this->assertEquals($value, FALSE, "FALSE expected");
    }


    /**
     * test if getDate parses a date
     * expected array
     */
    public function testgetDateParsingFailed()
    {
        try {
            $value = Zend_Locale_Format::getDate('no content');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
    }
    

    /**
     * test if getDate parses a date
     * expected array
     */
    public function testgetDateParsing()
    {
        $value = Zend_Locale_Format::getDate('10.10.06');
        $this->assertEquals(is_array($value), true, "array expected");
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing2()
    {
        $value = Zend_Locale_Format::getDate('10.10.06','dd.MM.yy');
        $this->assertEquals(count($value), 3, "array with 3 tags expected");
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing3()
    {
        $value = Zend_Locale_Format::getDate('10.11.06','dd.MM.yy');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 06, 'Year 06 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing4()
    {
        $value = Zend_Locale_Format::getDate('10.11.2006','dd.MM.yy');
        $this->assertSame($value['day'], 10, 'Day 10 expected');
        $this->assertSame($value['month'], 11, 'Month 11 expected');
        $this->assertSame($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing5()
    {
        $value = Zend_Locale_Format::getDate('2006.13.01','dd.MM.yy');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing6()
    {
        $value = Zend_Locale_Format::getDate('2006.01.13','dd.MM.yy');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing7()
    {
        $value = Zend_Locale_Format::getDate('101106','ddMMyy');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing8()
    {
        $value = Zend_Locale_Format::getDate('10112006','ddMMyyyy');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing9()
    {
        $value = Zend_Locale_Format::getDate('10 Nov 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing10()
    {
        $value = Zend_Locale_Format::getDate('10 November 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing11()
    {
        $value = Zend_Locale_Format::getDate('November 10 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing12()
    {
        $value = Zend_Locale_Format::getDate('Nov 10 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing13()
    {
        $value = Zend_Locale_Format::getDate('2006 10 Nov','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
/*
 * @todo failed test, auto completion doesnt work for this case
    public function testgetDateParsing14()
    {
        $value = Zend_Locale_Format::getDate('2006 Nov 10','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }
*/

    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing15()
    {
        $value = Zend_Locale_Format::getDate('10.11.06','yy.dd.MM');
        $this->assertEquals($value['day'], 11, 'Day 11 expected');
        $this->assertEquals($value['month'], 6, 'Month 6 expected');
        $this->assertEquals($value['year'], 10, 'Year 10 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing16()
    {
        $value = Zend_Locale_Format::getDate('10.11.06','dd.yy.MM');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 6, 'Month 6 expected');
        $this->assertEquals($value['year'], 11, 'Year 11 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing17()
    {
        $value = Zend_Locale_Format::getDate('10.11.06', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing18()
    {
        $value = Zend_Locale_Format::getDate('10.11.2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing19()
    {
        $value = Zend_Locale_Format::getDate('2006.13.01', false, 'de_AT');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing20()
    {
        $value = Zend_Locale_Format::getDate('2006.01.13', false, 'de_AT');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing21()
    {
        $value = Zend_Locale_Format::getDate('101106', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing22()
    {
        $value = Zend_Locale_Format::getDate('10112006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing23()
    {
        $value = Zend_Locale_Format::getDate('10 Nov 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing24()
    {
        $value = Zend_Locale_Format::getDate('10 November 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing25()
    {
        $value = Zend_Locale_Format::getDate('November 10 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing26()
    {
        $value = Zend_Locale_Format::getDate('Nov 10 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
    public function testgetDateParsing27()
    {
        $value = Zend_Locale_Format::getDate('2006 10 Nov', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test if getDate parses a date with fixed format
     * expected array
     */
/*
 * @todo failed test, auto completion doesnt work for this case
    public function testgetDateParsing28()
    {
        $value = Zend_Locale_Format::getDate('2006 Nov 10', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }
*/


    /**
     * test if getTime parses a time
     * expected Exception
     */
    public function testgetTimeParsingFailed()
    {
        try {
            $value = Zend_Locale_Format::getTime('no content');
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
    }


    /**
     * test if getTime parses a time
     * expected array
     */
    public function testgetTimeParsing()
    {
        $value = Zend_Locale_Format::getTime('13:14:55', 'HH:mm:ss');
        $this->assertEquals(is_array($value), true, "array expected");
    }


    /**
     * test if getTime parses a time
     * expected array
     */
    public function testgetTimeParsingFormat2()
    {
        $value = Zend_Locale_Format::getTime('11:14:55 am', 'h:mm:ss a');
        $this->assertEquals(is_array($value), true, "array expected");
    }


    /**
     * test if getTime parses a time
     * expected Exception
     */
    public function testgetTimeFormatFailed()
    {
        try {
            $value = Zend_Locale_Format::getTime('13:14:55', 'nocontent');
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
    }
    

    /**
     * test if getTime parses a time with fixed format
     * expected array
     */
    public function testgetTimeParsing2()
    {
        $value = Zend_Locale_Format::getTime('13:14:55','HH:mm:ss');
        $this->assertEquals(count($value), 3, "array with 3 tags expected");
    }


    /**
     * test if getTime parses a time with fixed format
     * expected array
     */
    public function testgetTimeParsing3()
    {
        $value = Zend_Locale_Format::getTime('13:14:55','HH:mm:ss');
        $this->assertEquals($value['hour'], 13, 'Hour 13 expected');
        $this->assertEquals($value['minute'], 14, 'Minute 14 expected');
        $this->assertEquals($value['second'], 55, 'Second 55 expected');
    }


    /**
     * test if getTime parses a time with fixed format
     * expected array
     */
    public function testgetTimeParsing4()
    {
        $value = Zend_Locale_Format::getTime('131455','HH:mm:ss');
        $this->assertEquals($value['hour'], 13, 'Hour 13 expected');
        $this->assertEquals($value['minute'], 14, 'Minute 14 expected');
        $this->assertEquals($value['second'], 55, 'Second 55 expected');
    }


    /**
     * test isDate
     * expected true
     */
    public function testIsDate()
    {
        $value = Zend_Locale_Format::isDate('13.Nov.2006',false,'de_AT');
        $this->assertTrue($value, "true expected");
    }


    /**
     * test isDate
     * expected true
     */
    public function testIsDateWithoutMonth()
    {
        $value = Zend_Locale_Format::isDate('13.XXX.2006', false, 'ar_EG');
        $this->assertTrue($value, "true expected");
    }


    /**
     * test isDate
     * expected false
     */
    public function testIsDateFailed()
    {
        $value = Zend_Locale_Format::isDate('nodate');
        $this->assertFalse($value, "false expected");
    }


    /**
     * test isTime
     * expected true
     */
    public function testIsTime()
    {
        $value = Zend_Locale_Format::isTime('13:10:55', false, 'de_AT');
        $this->assertTrue($value, "true expected");
    }


    /**
     * test isTime
     * expected true
     */
    public function testIsTimeAR()
    {
        $value = Zend_Locale_Format::isTime('11:10:55 am', false, 'ar_EG');
        $this->assertTrue($value, "true expected");
    }


    /**
     * test isTime
     * expected false
     */
    public function testIsTimeFailed()
    {
        $value = Zend_Locale_Format::isTime('notime');
        $this->assertFalse($value, "false expected");
    }
}