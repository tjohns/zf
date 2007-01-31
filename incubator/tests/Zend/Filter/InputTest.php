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
 * @see Zend_Filter_Input
 */
require_once 'Zend/Filter/Input.php';


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
class Zend_Filter_InputTest extends PHPUnit_Framework_TestCase
{
    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetAlpha()
    {
        $source = array('a1b2c3');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('abc', $filter->getAlpha(0));
        $this->assertFalse($filter->getAlpha(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetAlnum()
    {
        $source = array('a1!b2@c3#');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('a1b2c3', $filter->getAlnum(0));
        $this->assertFalse($filter->getAlnum(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetDigits()
    {
        $source = array('a1b2c3');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('123', $filter->getDigits(0));
        $this->assertFalse($filter->getDigits(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetDir()
    {
        $source = array('/path/to/index');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('/path/to', $filter->getDir(0));
        $this->assertFalse($filter->getDir(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetInt()
    {
        $source = array('123');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals(123, $filter->getInt(0));
        $this->assertFalse($filter->getInt(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetPath()
    {
        $source = array(dirname(__FILE__) . '/_files/file.1');
        $filter = new Zend_Filter_Input($source);
        $this->assertContains('file.1', $filter->getPath(0));
        $this->assertFalse($filter->getPath(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testGetRaw()
    {
        $source = array('a b c');
        $filter = new Zend_Filter_Input($source);
        $this->assertContains('a b c', $filter->getRaw(0));
        $this->assertFalse($filter->getRaw(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestAlnum()
    {
        $source = array('a1b2c3', 'a1!b2@c3#');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('a1b2c3', $filter->testAlnum(0));
        $this->assertFalse($filter->testAlnum(1));
        $this->assertFalse($filter->testAlnum(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestAlpha()
    {
        $source = array('abc', 'a1b2c3');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('abc', $filter->testAlpha(0));
        $this->assertFalse($filter->testAlpha(1));
        $this->assertFalse($filter->testAlpha(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestBetween()
    {
        $source = array(3, 4);
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals(3, $filter->testBetween(0, 1, 3));
        $this->assertFalse($filter->testBetween(1, 1, 3));
        $this->assertFalse($filter->testBetween(2, 1, 3));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestCcnum()
    {
        $source = array('4929000000006', '12345');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('4929000000006', $filter->testCcnum(0));
        $this->assertFalse($filter->testCcnum(1));
        $this->assertFalse($filter->testCcnum(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestDate()
    {
        $source = array('2007-01-01', 'author');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('2007-01-01', $filter->testDate(0));
        $this->assertFalse($filter->testDate(1));
        $this->assertFalse($filter->testDate(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestDigits()
    {
        $source = array('123', 'abc123');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('123', $filter->testDigits(0));
        $this->assertFalse($filter->testDigits(1));
        $this->assertFalse($filter->testDigits(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestEmail()
    {
        $source = array('webmaster@example.com', 'webmaster@a');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('webmaster@example.com', $filter->testEmail(0));
        $this->assertFalse($filter->testEmail(1));
        $this->assertFalse($filter->testEmail(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestFloat()
    {
        $source = array(1.23, 'author');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals(1.23, $filter->testFloat(0));
        $this->assertFalse($filter->testFloat(1));
        $this->assertFalse($filter->testFloat(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestGreaterThan()
    {
        $source = array(11, 10);
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals(11, $filter->testGreaterThan(0, 10));
        $this->assertFalse($filter->testGreaterThan(1, 10));
        $this->assertFalse($filter->testGreaterThan(2, 10));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestHex()
    {
        $source = array('a1B2c3', 'author');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('a1B2c3', $filter->testHex(0));
        $this->assertFalse($filter->testHex(1));
        $this->assertFalse($filter->testHex(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestHostname()
    {
        $source = array('example.com', 'A&B');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('example.com', $filter->testHostname(0));
        $this->assertFalse($filter->testHostname(1));
        $this->assertFalse($filter->testHostname(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestInt()
    {
        $source = array('123', 'A&B');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('123', $filter->testInt(0));
        $this->assertFalse($filter->testInt(1));
        $this->assertFalse($filter->testInt(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestIp()
    {
        $source = array('1.2.3.4', 'A&B');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('1.2.3.4', $filter->testIp(0));
        $this->assertFalse($filter->testIp(1));
        $this->assertFalse($filter->testIp(2));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestLessThan()
    {
        $source = array(9, 10);
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals(9, $filter->testLessThan(0, 10));
        $this->assertFalse($filter->testLessThan(1, 10));
        $this->assertFalse($filter->testLessThan(2, 10));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestOneOf()
    {
        $source = array(1, 2);
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals(1, $filter->testOneOf(0, array(1)));
        $this->assertFalse($filter->testOneOf(1, array(1)));
        $this->assertFalse($filter->testOneOf(2, array(1)));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testTestRegex()
    {
        $source = array('a', 2);
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('a', $filter->testRegex(0, '/[a-z]/'));
        $this->assertFalse($filter->testRegex(1, '/[a-z]/'));
        $this->assertFalse($filter->testRegex(2, '/[a-z]/'));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testNoTags()
    {
        $source = array('<b>word</b>');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('word', $filter->noTags(0));
        $this->assertFalse($filter->noTags(1));
    }

    /**
     * @deprecated since 0.8.0
     * @return void
     */
    public function testNoPath()
    {
        $source = array('/path/to/index.php');
        $filter = new Zend_Filter_Input($source);
        $this->assertEquals('index.php', $filter->noPath(0));
        $this->assertFalse($filter->noPath(1));
    }
}
