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
class FilterTest extends PHPUnit2_Framework_TestCase
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
}
