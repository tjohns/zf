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
     * test getNumber
     * expected integer
     */
    public function testGetNumber()
    {
        $this->assertEquals(Zend_Locale_Format::getNumber(0), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(-1234567), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(1234567), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(0.1234567), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(-1234567.12345), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber(1234567.12345), 1234567.12345, "value 1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('0', 'de'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1234567', 'de'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1234567', 'de'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('0,1234567', 'de'), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1.234.567,12345', 'de'), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1.234.567,12345', 'de'), 1234567.12345, "value 1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('0', 'de_AT'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1234567', 'de_AT'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1.234.567', 'de_AT'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('0,1234567', 'de_AT'), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('-1.234.567,12345', 'de_AT'), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getNumber('1.234.567,12345', 'de_AT'), 1234567.12345, "value 1234567.12345 expected");
    }


    /**
     * test to number
     * expected string
     */
    public function testToNumber()
    {
        $this->assertEquals(Zend_Locale_Format::toNumber(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0, 'de'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0, 'de_AT'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567, 'de_AT'), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567, 'de_AT'), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(0.1234567, 'de_AT'), '0,1234567', "string 0,1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, 'de_AT'), '-1.234.567,12345', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, 'de_AT'), '1.234.567,12345', "value 1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, 'ar_QA'), '1234567٫12345', "value 1234567٫12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, 'ar_QA'), '1234567٫12345-', "value 1234567٫12345- expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(1234567.12345, 'dz_BT'), '12,34,567.12345', "value 12,34,567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(-1234567.12345, 'mk_MK'), '-(1.234.567,12345)', "value -(1.234.567,12345) expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(452.25, 'en_US'), '452.25', "value 452.25 expected");
        $this->assertEquals(Zend_Locale_Format::toNumber(54321.1234, 'en_US'), '54,321.1234', "value 54,321.1234 expected");
    }


    /**
     * test isNumber
     * expected boolean
     */
    public function testIsNumber()
    {
        $this->assertEquals(Zend_Locale_Format::isNumber('-1.234.567,12345', 'de_AT'), true, "true expected");
        $this->assertEquals(Zend_Locale_Format::isNumber('textwithoutnumber', 'de_AT'), false, "false expected");
    }


    /**
     * test getFloat
     * expected exception
     */
    public function testgetFloat()
    {
        try {
            $value = Zend_Locale_Format::getFloat('nocontent');
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(Zend_Locale_Format::getFloat(0), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(-1234567), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(1234567), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(0.1234567), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(-1234567.12345), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat(1234567.12345), 1234567.12345, "value 1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0', 'de'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1234567', 'de'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1234567', 'de'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0,1234567', 'de'), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1.234.567,12345', 'de'), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', 'de'), 1234567.12345, "value 1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0', 'de_AT'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1234567', 'de_AT'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567', 'de_AT'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0,1234567', 'de_AT'), 0.1234567, "value 0.1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1.234.567,12345', 'de_AT'), -1234567.12345, "value -1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', 'de_AT'), 1234567.12345, "value 1234567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0', 2, 'de_AT'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1234567', 2, 'de_AT'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567', 2, 'de_AT'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('0,1234567', 2, 'de_AT'), 0.12, "value 0.12 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('-1.234.567,12345', 2, 'de_AT'), -1234567.12, "value -1234567.12 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', 2, 'de_AT'), 1234567.12, "value 1234567.12 expected");
        $this->assertEquals(Zend_Locale_Format::getFloat('1.234.567,12345', 7, 'de_AT'), '1234567.12345', "value 1234567.12345 expected");
    }


    /**
     * test toFloat
     * expected string
     */
    public function testToFloat()
    {
        $this->assertEquals(Zend_Locale_Format::toFloat(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0, 'de'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0, 'de_AT'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567, 'de_AT'), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567, 'de_AT'), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0.1234567, 'de_AT'), '0,1234567', "string 0,1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, 'de_AT'), '-1.234.567,12345', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, 'de_AT'), '1.234.567,12345', "value 1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, 'ar_QA'), '1234567٫12345', "value 1234567٫12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, 'ar_QA'), '1234567٫12345-', "value 1234567٫12345- expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, 'dz_BT'), '12,34,567.12345', "value 12,34,567.12345 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, 'mk_MK'), '-(1.234.567,12345)', "value -(1.234.567,12345) expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0, 2, 'de_AT'), '0,00', "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567, 2, 'de_AT'), '-1.234.567,00', "value -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567, 2, 'de_AT'), '1.234.567,00', "value 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(0.1234567, 2, 'de_AT'), '0,12', "value 0,12 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(-1234567.12345, 2, 'de_AT'), '-1.234.567,12', "value -1.234.567,12 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, 2, 'de_AT'), '1.234.567,12', "value 1.234.567,12 expected");
        $this->assertEquals(Zend_Locale_Format::toFloat(1234567.12345, 7, 'de_AT'), '1.234.567,1234500', "value 1.234.567,12345 expected");
    }

    
    /**
     * test isFloat
     * expected boolean
     */
    public function testIsFloat()
    {
        $this->assertEquals(Zend_Locale_Format::isFloat('-1.234.567,12345', 'de_AT'), true, "true expected");
        $this->assertEquals(Zend_Locale_Format::isFloat('textwithoutnumber', 'de_AT'), false, "false expected");
    }


    /**
     * test getInteger
     * expected integer
     */
    public function testgetInteger()
    {
        $this->assertEquals(Zend_Locale_Format::getInteger(0), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(-1234567), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(1234567), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(0.1234567), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(-1234567.12345), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger(1234567.12345), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('0', 'de'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1234567', 'de'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1234567', 'de'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('0,1234567', 'de'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1.234.567,12345', 'de'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1.234.567,12345', 'de'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('0', 'de_AT'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1234567', 'de_AT'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1.234.567', 'de_AT'), 1234567, "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('0,1234567', 'de_AT'), 0, "value 0 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('-1.234.567,12345', 'de_AT'), -1234567, "value -1234567 expected");
        $this->assertEquals(Zend_Locale_Format::getInteger('1.234.567,12345', 'de_AT'), 1234567, "value 1234567 expected");
    }


    /**
     * test toInteger
     * expected string
     */
    public function testtoInteger()
    {
        $this->assertEquals(Zend_Locale_Format::toInteger(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(0, 'de'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(0, 'de_AT'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567, 'de_AT'), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567, 'de_AT'), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(0.1234567, 'de_AT'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567.12345, 'de_AT'), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567.12345, 'de_AT'), '1.234.567', "value 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567.12345, 'ar_QA'), '1234567', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567.12345, 'ar_QA'), '1234567-', "value 1234567- expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(1234567.12345, 'dz_BT'), '12,34,567', "value 12,34,567 expected");
        $this->assertEquals(Zend_Locale_Format::toInteger(-1234567.12345, 'mk_MK'), '-(1.234.567)', "value -(1.234.567) expected");
    }


    /**
     * test isInteger
     * expected boolean
     */
    public function testIsInteger()
    {
        $this->assertEquals(Zend_Locale_Format::isInteger('-1.234.567,12345', 'de_AT'), TRUE, "TRUE expected");
        $this->assertEquals(Zend_Locale_Format::isInteger('textwithoutnumber', 'de_AT'), FALSE, "FALSE expected");
    }


    /**
     * test getDate
     * expected array
     */
    public function testgetDate()
    {
        try {
            $value = Zend_Locale_Format::getDate('no content');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        $this->assertEquals(is_array(Zend_Locale_Format::getDate('10.10.06')), true, "array expected");
        $this->assertEquals(count(Zend_Locale_Format::getDate('10.10.06','dd.MM.yy')), 4, "array with 4 tags expected");

        $value = Zend_Locale_Format::getDate('10.11.06','dd.MM.yy');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 06, 'Year 06 expected');

        $value = Zend_Locale_Format::getDate('10.11.2006','dd.MM.yy');
        $this->assertSame($value['day'], 10, 'Day 10 expected');
        $this->assertSame($value['month'], 11, 'Month 11 expected');
        $this->assertSame($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('2006.13.01','dd.MM.yy');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('2006.13.01','dd.MM.yy');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('2006.01.13','dd.MM.yy');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('2006.01.13','dd.MM.yy');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('101106','ddMMyy');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10112006','ddMMyyyy');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 Nov 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 November 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('November 10 2006','dd.MMM.yy', 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('November 10 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006','dd.MMM.yy', 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('Nov 10 2006','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('2006 10 Nov','dd.MMM.yy', 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('2006 10 Nov','dd.MMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

// @todo failed test, auto completion doesnt work for this case
//        $value = Zend_Locale_Format::getDate('2006 Nov 10','dd.MMM.yy', 'de_AT');
//        $this->assertEquals($value['day'], 10, 'Day 10 expected');
//        $this->assertEquals($value['month'], 11, 'Month 11 expected');
//        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10.11.06','yy.dd.MM');
        $this->assertEquals($value['day'], 11, 'Day 11 expected');
        $this->assertEquals($value['month'], 6, 'Month 6 expected');
        $this->assertEquals($value['year'], 10, 'Year 10 expected');

        $value = Zend_Locale_Format::getDate('10.11.06','dd.yy.MM');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 6, 'Month 6 expected');
        $this->assertEquals($value['year'], 11, 'Year 11 expected');

        $value = Zend_Locale_Format::getDate('10.11.06', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10.11.2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('2006.13.01', false, 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('2006.13.01', false, 'de_AT');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('2006.01.13', false, 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('2006.01.13', false, 'de_AT');
        $this->assertEquals($value['day'], 13, 'Day 13 expected');
        $this->assertEquals($value['month'], 1, 'Month 1 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('101106', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 6, 'Year 6 expected');

        $value = Zend_Locale_Format::getDate('10112006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 Nov 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('10 November 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('November 10 2006', false, 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('November 10 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('April 10 2006', false, 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('April 10 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 4, 'Month 4 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', false, 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('Nov 10 2006', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');


        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', false, 'de_AT');
            $this->fail("no date expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getCorrectableDate('2006 10 Nov', false, 'de_AT');
        $this->assertEquals($value['day'], 10, 'Day 10 expected');
        $this->assertEquals($value['month'], 11, 'Month 11 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('01.April.2006', 'dd.MMMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 1, 'Day 1 expected');
        $this->assertEquals($value['month'], 4, 'Month 4 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');

        $value = Zend_Locale_Format::getDate('Montag, 01.April.2006', 'EEEE, dd.MMMM.yy', 'de_AT');
        $this->assertEquals($value['day'], 1, 'Day 1 expected');
        $this->assertEquals($value['month'], 4, 'Month 4 expected');
        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
        
// @todo failed test, auto completion doesnt work for this case
//        $value = Zend_Locale_Format::getDate('2006 Nov 10', false, 'de_AT');
//        $this->assertEquals($value['day'], 10, 'Day 10 expected');
//        $this->assertEquals($value['month'], 11, 'Month 11 expected');
//        $this->assertEquals($value['year'], 2006, 'Year 2006 expected');
    }


    /**
     * test getTime
     * expected array
     */
    public function testgetTime()
    {
        try {
            $value = Zend_Locale_Format::getTime('no content');
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(is_array(Zend_Locale_Format::getTime('13:14:55', 'HH:mm:ss')), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('11:14:55 am', 'h:mm:ss a', 'en')), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('12:14:55 am', 'h:mm:ss a', 'en')), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('11:14:55 pm', 'h:mm:ss a', 'en')), true, "array expected");
        $this->assertEquals(is_array(Zend_Locale_Format::getTime('12:14:55 pm', 'h:mm:ss a', 'en')), true, "array expected");

        try {
            $value = Zend_Locale_Format::getTime('13:14:55', 'nocontent');
            $this->fail("no time expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(count(Zend_Locale_Format::getTime('13:14:55','HH:mm:ss')), 4, "array with 4 tags expected");

        $value = Zend_Locale_Format::getTime('13:14:55','HH:mm:ss');
        $this->assertEquals($value['hour'], 13, 'Hour 13 expected');
        $this->assertEquals($value['minute'], 14, 'Minute 14 expected');
        $this->assertEquals($value['second'], 55, 'Second 55 expected');

        $value = Zend_Locale_Format::getTime('131455','HH:mm:ss');
        $this->assertEquals($value['hour'], 13, 'Hour 13 expected');
        $this->assertEquals($value['minute'], 14, 'Minute 14 expected');
        $this->assertEquals($value['second'], 55, 'Second 55 expected');
    }


    /**
     * test isDate
     * expected boolean
     */
    public function testIsDate()
    {
        $this->assertTrue(Zend_Locale_Format::isDate('13.Nov.2006', null, 'de_AT'), "true expected");
        $this->assertFalse(Zend_Locale_Format::isDate('13.XXX.2006', null, 'ar_EG'), "false expected");
        $this->assertFalse(Zend_Locale_Format::isDate('nodate'), "false expected");

        $this->assertFalse(Zend_Locale_Format::isDate('20.01.2006', 'M-d-y'), "false expected");
        $this->assertTrue(Zend_Locale_Format::isDate('20.01.2006', 'd-M-y'), "true expected");
    }


    /**
     * test isTime
     * expected boolean
     */
    public function testIsTime()
    {
        $this->assertTrue(Zend_Locale_Format::isTime('13:10:55', false, 'de_AT'), "true expected");
        $this->assertTrue(Zend_Locale_Format::isTime('11:10:55 am', false, 'ar_EG'), "true expected");
        $this->assertFalse(Zend_Locale_Format::isTime('notime'), "false expected");
    }


    /**
     * test toNumberSystem
     * expected string
     */
    public function testToNumberSystem()
    {
        try {
            $value = Zend_Locale_Format::convertNumerals('١١٠', 'xxxx');
            $this->fail("no conversion expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::convertNumerals('١١٠', 'Arab', 'xxxx');
            $this->fail("no conversion expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(Zend_Locale_Format::convertNumerals('١١٠', 'Arab'), '110', "110 expected");
        $this->assertEquals(Zend_Locale_Format::convertNumerals('١١٠', 'Arab', 'Deva'), '११०', "११० expected");
        $this->assertEquals(Zend_Locale_Format::convertNumerals('110', 'Latin', 'Arab'), '١١٠', "١١٠ expected");
    }
    
    /**
     * test toNumberFormat
     * expected string
     */
    public function testToNumberFormat()
    {
        $locale = new Zend_Locale('de_AT');
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(0), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(0, 'de'), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(0, $locale), '0', "string 0 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(-1234567, 'de_AT'), '-1.234.567', "string -1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567, 'de_AT'), '1.234.567', "string 1.234.567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(0.1234567, 'de_AT'), '0,1234567', "string 0,1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(-1234567.12345, 'de_AT'), '-1.234.567,12345', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, 'de_AT'), '1.234.567,12345', "value 1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '##0', 'de_AT'), '1234567', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '#,#0', 'de_AT'), '1.23.45.67', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '##0.00', 'de_AT'), '1234567,12', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '##0.###', 'de_AT'), '1234567,12345', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '#,#0', 'de_AT'), '1.23.45.67', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '#,##,##0', 'de_AT'), '12.34.567', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '#,##0.00', 'de_AT'), '1.234.567,12', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '#,#0.00', 'de_AT'), '1.23.45.67,12', "value 1234567 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(-1234567.12345, '##0;##0-', 'de_AT'), '1234567-', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567.12345, '##0;##0-', 'de_AT'), '1234567', "string -1.234.567,12345 expected");
        $this->assertEquals(Zend_Locale_Format::toNumberFormat(1234567, '#,##0.00', 'de_AT'), '1.234.567,00', "value 1234567,00 expected");
    }
}
