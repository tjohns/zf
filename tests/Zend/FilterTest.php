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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Filter
 */
require_once 'Zend/Filter.php';


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter object
     *
     * @var Zend_Filter
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter();
    }

    /**
     * Ensures expected return value from empty filter chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $value = 'something';
        $this->assertEquals($value, $this->_filter->filter($value));
    }

    /**
     * Ensures that filters are executed in the expected order (FIFO)
     *
     * @return void
     */
    public function testFilterOrder()
    {
        $this->_filter->addFilter(new Zend_FilterTest_LowerCase())
                      ->addFilter(new Zend_FilterTest_StripUpperCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $this->_filter->filter($value));
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsAlpha()
    {
        $this->assertTrue(Zend_Filter::isAlpha('abcXYZ'), '"abcXYZ" is alphabetic.');
        $this->assertFalse(Zend_Filter::isAlpha('abc xyz'), '"abc xyz" is not alphabetic.');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsAlnum()
    {
        $this->assertTrue(Zend_Filter::isAlnum('abc123'), '"abc123" is alphanumeric.');
        $this->assertFalse(Zend_Filter::isAlnum('abc xyz'), '"abc 123" is not alphanumeric.');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsCCNum()
    {
        $this->assertTrue(Zend_Filter::isCcnum('4929000000006'), '"4929000000006" is a valid Visa cc num.');
        $this->assertTrue(Zend_Filter::isCcnum('5404000000000001'), '"5404000000000001" is a valid Mastercard cc num.');
        $this->assertTrue(Zend_Filter::isCcnum('374200000000004'), '"374200000000004" is a valid Amex cc num.');
        $this->assertFalse(Zend_Filter::isCcnum('4444555566667777'), '"4444555566667777" is not a valid cc num.');
        $this->assertFalse(Zend_Filter::isCcnum('ABCDEF'), '"ABCDEF" is not a valid cc num.');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsBetween()
    {
        $this->assertTrue(Zend_Filter::isBetween(10, 1, 20, FALSE), '"10" is between "1" and "20" non inclusive');
        $this->assertTrue(Zend_Filter::isBetween(10, 1, 10), '"10" is between "1" and "20" inclusive');
        $this->assertFalse(Zend_Filter::isBetween(10, 1, 9, FALSE), '"10" is not between "1" and "9" inclusive');
        $this->assertFalse(Zend_Filter::isBetween(10, 1, 9), '"10" is not between "1" and "9" inclusive');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsDigits()
    {
        $this->assertTrue(Zend_Filter::isDigits(123456), '123456 is just digits');
        $this->assertTrue(Zend_Filter::isDigits(1), '1 is just digits');
        $this->assertFalse(Zend_Filter::isDigits('12345A'), '"12345A" is not just digits');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsGreaterThan()
    {
        $this->assertTrue(Zend_Filter::isGreaterThan(100, 50), '100 is greater than 50');
        $this->assertFalse(Zend_Filter::isGreaterThan(50, 100), '50 is not greater than 100');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testIsLessThan()
    {
        $this->assertTrue(Zend_Filter::isLessThan(50, 100), '50 is less than 100');
        $this->assertFalse(Zend_Filter::isLessThan(100, 50), '100 is not less than 50');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testisHex()
    {
        $this->assertTrue(Zend_Filter::isHex('AB10BC99'), '"AB10BC99" is a valid hex number');
        $this->assertFalse(Zend_Filter::isHex('ABK'), '"ABK" is not a valid hex number');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testisDate()
    {
        $this->assertTrue(Zend_Filter::isDate('1997-07-16'), '"1997-07-16" is a valid date');
        $this->assertFalse(Zend_Filter::isDate('1977-20-08'), '"1977-20-08" is not a valid date');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testisFloat()
    {
        $this->assertTrue(Zend_Filter::isFloat(1.2e3), '"1.2e3" is a valid float number');
        $this->assertFalse(Zend_Filter::isFloat('ABC'), '"ABC" is not a valid float number');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testisInt()
    {
        $this->assertTrue(Zend_Filter::isInt(100), '100 is a valid integer');
        $this->assertFalse(Zend_Filter::isInt(1.23), '1.2e3 is not a valid integer');
    }

    /**
     * @deprecated since 0.8.0
     * @return     void
     */
    public function testOthers()
    {
        $testData = array(
            array('getAlpha', 'a1b2c3', 'abc'),
            array('getAlnum', 'a1!b2@c3#', 'a1b2c3'),
            array('getDigits', 'a1b2c3', '123'),
            array('getDir', '/path/to/index', '/path/to'),
            array('getInt', '123', 123),
            array('getPath', dirname(__FILE__) . '/Filter/_files/file.1', 'file.1', 'contains'),
            array('isEmail', 'webmaster@example.com', true),
            array('isHostname', 'example.com', true),
            array('isIp', '1.2.3.4', true),
            array('isOneOf', 2, true, null, array(1, 2, 3)),
            array('isRegex', 'abc', true, null, '/^[a-z]+$/'),
            array('noTags', '<b>word</b>', 'word'),
            array('noPath', '/path/to/index.php', 'index.php')
            );
        foreach ($testData as $entry) {
            $callback = array('Zend_Filter', $entry[0]);
            if (isset($entry[4])) {
                $parameters = array_merge(array($entry[1]), array_slice($entry, 4));
                $result = call_user_func_array($callback, $parameters);
            } else {
                $result = call_user_func($callback, $entry[1]);
            }
            if (isset($entry[3]) && 'contains' === $entry[3]) {
                $this->assertContains($entry[2], $result);
            } else {
                $this->assertEquals($entry[2], $result);
            }
        }
    }
}


class Zend_FilterTest_LowerCase implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return strtolower($value);
    }
}


class Zend_FilterTest_StripUpperCase implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return preg_replace('/[A-Z]/', '', $value);
    }
}