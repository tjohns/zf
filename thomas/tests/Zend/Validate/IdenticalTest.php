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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_IdenticalTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Validate_Identical */
require_once 'Zend/Validate/Identical.php';

/**
 * Zend_Validate_Identical
 *
 * @category   Zend
 * @package    UnitTests
 * @uses       Zend_Validate_Identical
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: IdenticalTest.php 11973 2008-10-15 16:00:56Z matthew $
 */
class Zend_Validate_IdenticalTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Validate_IdenticalTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->validator = new Zend_Validate_Identical();
    }

    public function testTokenInitiallyNull()
    {
        $this->assertNull($this->validator->getToken());
    }

    public function testCanSetToken()
    {
        $this->testTokenInitiallyNull();
        $this->validator->setToken('foo');
        $this->assertEquals('foo', $this->validator->getToken());
    }

    public function testCanSetTokenViaConstructor()
    {
        $validator = new Zend_Validate_Identical('foo');
        $this->assertEquals('foo', $validator->getToken());
    }

    public function testValidatingWhenTokenNullReturnsFalse()
    {
        $this->assertFalse($this->validator->isValid('foo'));
    }

    public function testValidatingWhenTokenNullSetsMissingTokenMessage()
    {
        $this->testValidatingWhenTokenNullReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('missingToken', $messages));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueReturnsFalse()
    {
        $this->validator->setToken('foo');
        $this->assertFalse($this->validator->isValid('bar'));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueSetsNotSameMessage()
    {
        $this->testValidatingAgainstTokenWithNonMatchingValueReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('notSame', $messages));
    }

    public function testValidatingAgainstTokenWithMatchingValueReturnsTrue()
    {
        $this->validator->setToken('foo');
        $this->assertTrue($this->validator->isValid('foo'));
    }

    public function testValidatingAgainstTokenWithNonMatchingArrayReturnsFalse()
    {
        $this->assertFalse($this->validator->isValid(array('test1' => 'foo', 'test2' => 'foo')));
    }

    public function testValidatingAgainstTokenWithNonMatchingArrayElementReturnsFalse()
    {
        $element = array('foo' => 'bar');
        $this->validator->setToken('foo');
        $this->assertTrue($this->validator->isValid('bar', $element));
    }

    public function testCanSetTokenAndElementViaConstructor()
    {
        $validator = new Zend_Validate_Identical('foo', 'bar');
        $this->assertEquals('foo', $validator->getToken());
        $this->assertEquals('bar', $validator->getElement());
    }

    public function testValidatingMultidimensionalArrays()
    {
        $element1 = array('foo' => array('bar' => 'baz', 'cap' => 'cat'));
        $element2 = array('foo' => array('bar' => 'baz', 'cap' => 'cat'));
        $this->validator->setToken($element1);
        $this->assertTrue($this->validator->isValid($element2));
    }

    public function testValidatingMultidimensionalArraysToFalse()
    {
        $element1 = array('foo' => array('bar' => 'baz', 'cap' => 'cat'));
        $element2 = array('foo' => array('bar' => 'baz', 'cap' => 'cas'));
        $this->validator->setToken($element1);
        $this->assertFalse($this->validator->isValid($element2));
    }

    public function testValidatingMultidimensionalArraysAtValidation()
    {
        $element1 = array('foo' => array('bar' => 'baz', 'cap' => 'cat'));
        $element2 = array('foo' => array('bar' => 'baz', 'cap' => 'cat'));
        $this->assertTrue($this->validator->isValid($element1, $element2));
    }

    public function testValidatingObjects()
    {
        $element1 = new Zend_Validate_Identical('bar', 'baz');
        $element2 = new Zend_Validate_Identical('bar', 'baz');
        $this->validator->setToken($element1);
        $this->assertTrue($this->validator->isValid($element2));
    }

    public function testValidatingStringsWhenCallingIsValid()
    {
        $this->assertFalse($this->validator->isValid('foo', 'bar'));
        $this->assertTrue($this->validator->isValid('foo', 'foo'));
    }
}

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Zend_Validate_IdenticalTest::main') {
    Zend_Validate_IdenticalTest::main();
}
