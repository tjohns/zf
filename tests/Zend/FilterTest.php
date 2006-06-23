<?php
/**
 * @package    Zend_Filter
 * @subpackage UnitTests
 */

/**
 * Zend_Filter
 */
require_once 'Zend/Filter.php';

/**
 * PHPUnit2_Framework_TestCase
 */
require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * @package    Zend_Filter
 * @subpackage UnitTests
 */
class Zend_FilterTest extends PHPUnit2_Framework_TestCase
{
    public function testIsAlpha()
    {
        $this->assertTrue(Zend_Filter::isAlpha('abcXYZ'), '"abcXYZ" is alphabetic.');
        $this->assertFalse(Zend_Filter::isAlpha('abc xyz'), '"abc xyz" is not alphabetic.');
    }

    public function testIsAlnum()
    {
        $this->assertTrue(Zend_Filter::isAlnum('abc123'), '"abc123" is alphanumeric.');
        $this->assertFalse(Zend_Filter::isAlnum('abc xyz'), '"abc 123" is not alphanumeric.');
    }

    public function testIsCCNum()
    {
        $this->assertTrue(Zend_Filter::isCcnum('4929000000006'), '"4929000000006" is a valid Visa cc num.');
        $this->assertTrue(Zend_Filter::isCcnum('5404000000000001'), '"5404000000000001" is a valid Mastercard cc num.');
        $this->assertTrue(Zend_Filter::isCcnum('374200000000004'), '"374200000000004" is a valid Amex cc num.');
        $this->assertFalse(Zend_Filter::isCcnum('4444555566667777'), '"4444555566667777" is not a valid cc num.');
        $this->assertFalse(Zend_Filter::isCcnum('ABCDEF'), '"ABCDEF" is not a valid cc num.');
    }

    public function testIsBetween()
    {
        $this->assertTrue(Zend_Filter::isBetween(10, 1, 20, FALSE), '"10" is between "1" and "20" non inclusive');
        $this->assertTrue(Zend_Filter::isBetween(10, 1, 10), '"10" is between "1" and "20" inclusive');
        $this->assertFalse(Zend_Filter::isBetween(10, 1, 9, FALSE), '"10" is not between "1" and "9" inclusive');
        $this->assertFalse(Zend_Filter::isBetween(10, 1, 9), '"10" is not between "1" and "9" inclusive');
    }

    public function testIsZip()
    {
        $this->assertTrue(Zend_Filter::isZip('90210'), '"90210" is a valid zip code');
        $this->assertFalse(Zend_Filter::isZip('9501'), '"04600" is not a valid zip code');
    }

    public function testIsDigits()
    {
        $this->assertTrue(Zend_Filter::isDigits(123456), '123456 is just digits');
        $this->assertFalse(Zend_Filter::isDigits('12345A'), '"12345A" is not just digits');
    }

    public function testIsGreaterThan()
    {
        $this->assertTrue(Zend_Filter::isGreaterThan(100, 50), '100 is greater than 50');
        $this->assertFalse(Zend_Filter::isGreaterThan(50, 100), '50 is not greater than 100');
    }

    public function testIsLessThan()
    {
        $this->assertTrue(Zend_Filter::isLessThan(50, 100), '50 is less than 100');
        $this->assertFalse(Zend_Filter::isLessThan(100, 50), '100 is not less than 50');
    }

    public function testisHex()
    {
        $this->assertTrue(Zend_Filter::isHex('AB10BC99'), '"AB10BC99" is a valid hex number');
        $this->assertFalse(Zend_Filter::isHex('ABK'), '"ABK" is not a valid hex number');
    }

    public function testisName()
    {
        $this->assertTrue(Zend_Filter::isName('Mark'), '"Mark" is a valid name');
        $this->assertFalse(Zend_Filter::isName('100'), '"100" is not a valid name');
    }

    public function testisDate()
    {
        $this->assertTrue(Zend_Filter::isDate('1997-07-16'), '"1997-07-16" is a valid date');
        $this->assertFalse(Zend_Filter::isDate('1977-20-08'), '"1977-20-08" is not a valid date');
    }

    public function testisFloat()
    {
        $this->assertTrue(Zend_Filter::isFloat(1.2e3), '"1.2e3" is a valid float number');
        $this->assertFalse(Zend_Filter::isFloat('ABC'), '"ABC" is not a valid float number');
    }

    public function testisInt()
    {
        $this->assertTrue(Zend_Filter::isInt(100), '100 is a valid integer');
        $this->assertFalse(Zend_Filter::isInt(1.23), '1.2e3 is not a valid integer');
    }

    public function testisPhone()
    {
        $this->assertTrue(Zend_Filter::isPhone('6134123456'), '"6134123456" is a US Phone number');
        $this->assertFalse(Zend_Filter::isPhone('1004123456'), '"100123456" is not a valid US Phone number');
    }
}
