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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 4412 2007-04-06 21:17:32Z zendbot $
 */

/**
 * @see Zend_Filter_Input
 */
require_once 'Zend/Filter/Input.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

require_once 'PHPUnit/Framework/TestCase.php';

class Zend_Filter_InputTest extends PHPUnit_Framework_TestCase
{

    public function testFilterDeclareByScalar()
    {
        $data = array(
            'month' => '6abc '
        );
        $filters = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input($filters, null, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testFilterDeclareByArray()
    {
        $data = array(
            'month' => '6abc '
        );
        $filters = array(
            'month' => array('digits')
        );
        $input = new Zend_Filter_Input($filters, null, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testFilterDeclareByObject()
    {
        $data = array(
            'month' => '6abc '
        );
        Zend_Loader::loadClass('Zend_Filter_Digits');
        $filters = array(
            'month' => array(new Zend_Filter_Digits())
        );
        $input = new Zend_Filter_Input($filters, null, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testFilterDeclareByChain()
    {
        $data = array(
            'field1' => ' ABC '
        );
        $filters = array(
            'field1' => array('StringTrim', 'StringToLower')
        );
        $input = new Zend_Filter_Input($filters, null, $data);
        $this->assertEquals('abc', $input->field1);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testFilterWildcardRule()
    {
        $data = array(
            'field1'  => ' 12abc ',
            'field2'  => ' 24abc '
        );
        $filters = array(
            '*'       => 'stringTrim',
            'field1'  => 'digits'
        );
        $input = new Zend_Filter_Input($filters, null, $data);
        $this->assertEquals('12', $input->field1);
        $this->assertEquals('24abc', $input->field2);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testFilterMultiValue()
    {
        $data = array(
            'field1' => array('FOO', 'BAR', 'BaZ')
        );
        $filters = array(
            'field1' => 'StringToLower'
        );
        $input = new Zend_Filter_Input($filters, null, $data);
        $this->assertEquals(array('foo', 'bar', 'baz'), $input->field1);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorByScalar()
    {
        $data = array(
            'month' => '6'
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorByScalarInvalid()
    {
        $data = array(
            'month' => '6abc '
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $invalid = $input->getInvalid();
        $msg = $invalid['month'][0];
        $this->assertEquals("'6abc ' contains not only digit characters", $msg);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorByArray()
    {
        $data = array(
            'month' => '6'
        );
        $validators = array(
            'month' => array('digits')
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorByObject()
    {
        $data = array(
            'month' => '6'
        );
        Zend_Loader::loadClass('Zend_Validate_Digits');
        $validators = array(
            'month' => array(
                new Zend_Validate_Digits()
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $month = $input->month;
        $this->assertEquals('6', $month);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorByChain()
    {
        $data = array(
            'field1' => '50',
            'field2' => 'abc123',
            'field3' => 150,
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $btw = new Zend_Validate_Between(1, 100);
        $validators = array(
            'field1' => array('digits', $btw),
            'field2' => array('digits', $btw),
            'field3' => array('digits', $btw)
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertEquals(array('field2', 'field3'), array_keys($invalid));
        $this->assertEquals("'abc123' contains not only digit characters",
            $invalid['field2'][0]);
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $invalid['field3'][0]);
    }

    public function testValidatorWildcardRule()
    {
        $data = array(
            'field1'  => '123abc',
            'field2'  => '246abc'
        );
        $validators = array(
            '*'       => 'alnum',
            'field1'  => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertEquals('246abc', $input->field2);
        $this->assertNull($input->field1);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorMultiValue()
    {
        $data = array(
            'field1' => array('abc', 'def', 'ghi'),
            'field2' => array('abc', '123')
        );
        $validators = array(
            'field1' => 'alpha',
            'field2' => 'alpha'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertEquals(array('field2'), array_keys($invalid));
        $this->assertEquals("'123' has not only alphabetic characters",
            $invalid['field2'][0]);
    }

    public function testValidatorMultiField()
    {
        $data = array(
            'password1' => 'EREIAMJH',
            'password2' => 'EREIAMJH',
            'password3' => 'VESPER'
        );
        $validators = array(
            'rule1' => array(
                'StringEquals',
                'fields' => array('password1', 'password2')
            ),
            'rule2' => array(
                'StringEquals',
                'fields' => array('password1', 'password3')
            )
        );
        $options = array(
            Zend_Filter_Input::NAMESPACE => 'TestNamespace'
        );
        $ip = get_include_path();
        set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'_files'.PATH_SEPARATOR.$ip);
        $input = new Zend_Filter_Input(null, $validators, $data, $options);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        set_include_path($ip);
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertEquals(array('rule2'), array_keys($invalid));
        $this->assertEquals("Not all strings in the argument are equal",
            $invalid['rule2'][0]);
    }

    public function testValidatorBreakChain()
    {
        $data = array(
            'field1' => '150',
            'field2' => '150'
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $btw1 = new Zend_Validate_Between(1, 100);
        $btw2 = new Zend_Validate_Between(1, 125);
        $validators = array(
            'field1' => array($btw1, $btw2),
            'field2' => array($btw1, $btw2, Zend_Filter_Input::BREAK_CHAIN => true)
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertEquals(array('field1', 'field2'), array_keys($invalid));
        $this->assertEquals(2, count($invalid['field1']), 'Expected rule for field1 to break 2 validators');
        $this->assertEquals(1, count($invalid['field2']), 'Expected rule for field2 to break 1 validator');
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $invalid['field1'][0]);
        $this->assertEquals("'150' is not between '1' and '125', inclusively",
            $invalid['field1'][1]);
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $invalid['field2'][0]);
    }

    public function testValidatorAllowEmpty()
    {
        $data = array(
            'field1' => '',
            'field2' => ''
        );
        $validators = array(
            'field1' => array(
                'alpha',
                Zend_Filter_Input::ALLOW_EMPTY => false
            ),
            'field2' => array(
                'alpha',
                Zend_Filter_Input::ALLOW_EMPTY => true
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertNull($input->field1);
        $this->assertNotNull($input->field2);
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertEquals(array('field1'), array_keys($invalid));
        $this->assertEquals("'' has not only alphabetic characters", $invalid['field1'][0]);
    }

    public function testValidatorHasMissing()
    {
        $data = array();
        $validators = array(
            'month' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorFieldOptional()
    {
        $data = array();
        $validators = array(
            'month' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testValidatorGetMissing()
    {
        $data = array();
        $validators = array(
            'month' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $missing = $input->getMissing();
        $this->assertType('array', $missing);
        $this->assertThat($missing, $this->arrayHasKey('month'));
    }

    public function testValidatorHasUnknown()
    {
        $data = array(
            'unknown' => 'xxx'
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expecting hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expecting hasInvalid() to return false');
        $this->assertTrue($input->hasUnknown(), 'Expecting hasUnknown() to return true');
    }

    public function testValidatorGetUnknown()
    {
        $data = array(
            'unknown' => 'xxx'
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertTrue($input->hasUnknown(), 'Expected hasUnknown() to retrun true');
        $unknown = $input->getUnknown();
        $this->assertType('array', $unknown);
        $this->assertThat($unknown, $this->arrayHasKey('unknown'));
    }

    public function testAddNamespace()
    {
        $data = array(
            'field1' => 'abc',
            'field2' => '123',
            'field3' => '123'
        );
        $validators = array(
            'field1' => 'MyDigits',
            'field2' => 'MyDigits',
            'field3' => 'digits'
        );
        $options = array(
            Zend_Filter_Input::NAMESPACE => 'TestNamespace'
        );
        $ip = get_include_path();
        set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'_files'.PATH_SEPARATOR.$ip);
        $input = new Zend_Filter_Input(null, $validators, $data);
        $input->addNamespace('TestNamespace');
        $this->assertEquals('123', (string) $input->field2);
        $this->assertEquals('123', (string) $input->field3);
        set_include_path($ip);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertThat($invalid, $this->arrayHasKey('field1'));
        $this->assertEquals("'abc' contains not only digit characters", $invalid['field1'][0]);
    }

    public function testNamespaceExceptionClassNotFound()
    {
        $data = array(
            'field1' => 'abc'
        );
        $validators = array(
            'field1' => 'MyDigits'
        );
        // Do not add namespace on purpose, so MyDigits will not be found
        $input = new Zend_Filter_Input(null, $validators, $data);
        try {
            $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Could not find a class based on name 'MyDigits' extending Zend_Validate_Interface",
                $e->getMessage());
        }
    }

    public function testSetDefaultEscapeFilter()
    {
        $data = array(
            'field1' => ' ab&c '
        );
        $options = array(
            Zend_Filter_Input::ESCAPE_FILTER => 'StringTrim'
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $input->setDefaultEscapeFilter('StringTrim');
        $this->assertEquals('ab&c', $input->field1);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testSetDefaultEscapeFilterExceptionWrongClassType()
    {
        $input = new Zend_Filter_Input(null, null);
        try {
            $input->setDefaultEscapeFilter(new StdClass());
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Escape filter specified does not implement Zend_Filter_Interface", $e->getMessage());
        }
    }

    public function testOptionAllowEmpty()
    {
        $data = array(
            'field1' => ''
        );
        $validators = array(
            'field1' => 'alpha'
        );
        $options = array(
            Zend_Filter_Input::ALLOW_EMPTY => true
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertNotNull($input->field1);
        $this->assertEquals('', $input->field1);
    }

    public function testOptionBreakChain()
    {
        $data = array(
            'field1' => '150'
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $btw1 = new Zend_Validate_Between(1, 100);
        $btw2 = new Zend_Validate_Between(1, 125);
        $validators = array(
            'field1' => array($btw1, $btw2),
        );
        $options = array(
            Zend_Filter_Input::BREAK_CHAIN => true
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertEquals(array('field1'), array_keys($invalid));
        $this->assertEquals(1, count($invalid['field1']), 'Expected rule for field1 to break 1 validator');
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $invalid['field1'][0]);
    }

    public function testOptionEscapeFilter()
    {
        $data = array(
            'field1' => ' ab&c '
        );
        $options = array(
            Zend_Filter_Input::ESCAPE_FILTER => 'StringTrim'
        );
        $input = new Zend_Filter_Input(null, null, $data, $options);
        $this->assertEquals('ab&c', $input->field1);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testOptionNamespace()
    {
        $data = array(
            'field1' => 'abc',
            'field2' => '123',
            'field3' => '123'
        );
        $validators = array(
            'field1' => 'MyDigits',
            'field2' => 'MyDigits',
            'field3' => 'digits'
        );
        $options = array(
            Zend_Filter_Input::NAMESPACE => 'TestNamespace'
        );
        $ip = get_include_path();
        set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'_files'.PATH_SEPARATOR.$ip);
        $input = new Zend_Filter_Input(null, $validators, $data, $options);
        $this->assertEquals('123', (string) $input->field2);
        $this->assertEquals('123', (string) $input->field3);
        set_include_path($ip);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $invalid = $input->getInvalid();
        $this->assertType('array', $invalid);
        $this->assertThat($invalid, $this->arrayHasKey('field1'));
        $this->assertEquals("'abc' contains not only digit characters", $invalid['field1'][0]);
    }

    public function testOptionPresence()
    {
        $data = array(
            'field1' => '123'
            // field2 is missing deliberately
        );
        $validators = array(
            'field1' => 'Digits',
            'field2' => 'Digits'
        );
        $options = array(
            Zend_Filter_Input::PRESENCE => true
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);
        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $missing = $input->getMissing();
        $this->assertType('array', $missing);
        $this->assertEquals(array('field2'), array_keys($missing));
        $this->assertEquals("Field 'field2' is required by rule 'field2', but the field is missing", $missing['field2'][0]);
    }

    public function testOptionExceptionUnknown()
    {
        $options = array(
            'unknown' => 'xxx'
        );
        try {
            $input = new Zend_Filter_Input(null, null, null, $options);
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Unknown option 'unknown'", $e->getMessage());
        }
    }

    public function testGetEscaped()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $this->assertEquals('ab&amp;c', $input->getEscaped('field1'));
        $this->assertNull($input->getEscaped('field2'));
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testMagicGetEscaped()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $this->assertEquals('ab&amp;c', $input->field1);
        $this->assertNull($input->field2);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testGetEscapedMultiValue()
    {
        $data = array(
            'multiSelect' => array('C&H', 'B&O', 'AT&T')
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $multi = $input->getEscaped('multiSelect');
        $this->assertType('array', $multi);
        $this->assertEquals(3, count($multi));
        $this->assertEquals(array('C&amp;H', 'B&amp;O', 'AT&amp;T'), $multi);
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testGetUnescaped()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $this->assertEquals('ab&c', $input->getUnescaped('field1'));
        $this->assertNull($input->getUnescaped('field2'));
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testMagicIsset()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $this->assertTrue(isset($input->field1));
        $this->assertFalse(isset($input->field2));
        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
    }

    public function testProcess()
    {
        $data = array(
            'field1' => 'ab&c',
            'field2' => '123abc'
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
            $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
            $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        } catch (Zend_Exception $e) {
            $this->fail('Received Zend_Exception where none was expected');
        }
    }

    public function testProcessUnknownThrowsNoException()
    {
        $data = array(
            'field1' => 'ab&c',
            'field2' => '123abc',
            'field3' => 'unknown'
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
            $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
            $this->assertTrue($input->hasUnknown(), 'Expected hasUnknown() to retrun true');
        } catch (Zend_Exception $e) {
            $this->fail('Received Zend_Exception where none was expected');
        }
    }

    public function testProcessInvalidThrowsException()
    {
        $data = array(
            'field1' => 'ab&c',
            'field2' => 'abc' // invalid because no digits
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Input has invalid fields", $e->getMessage());
            $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
            $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
            $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        }
    }

    public function testProcessMissingThrowsException()
    {
        $data = array(
            'field1' => 'ab&c'
            // field2 is missing on purpose for this test
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Input has missing fields", $e->getMessage());
            $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
            $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
            $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        }
    }

}
