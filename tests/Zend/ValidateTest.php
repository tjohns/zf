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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Validate
 */
require_once 'Zend/Validate.php';


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_ValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate object
     *
     * @var Zend_Validate
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate();
    }

    /**
     * Ensures expected results from empty validator chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertTrue($this->_validator->isValid('something'));
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures expected behavior from a validator known to succeed
     *
     * @return void
     */
    public function testTrue()
    {
        $this->_validator->addValidator(new Zend_ValidateTest_True());
        $this->assertTrue($this->_validator->isValid(null));
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures expected behavior from a validator known to fail
     *
     * @return void
     */
    public function testFalse()
    {
        $this->_validator->addValidator(new Zend_ValidateTest_False());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('validation failed'), $this->_validator->getMessages());
    }

    /**
     * Ensures that a validator may break the chain
     *
     * @return void
     */
    public function testBreakChainOnFailure()
    {
        $this->_validator->addValidator(new Zend_ValidateTest_False(), true)
                         ->addValidator(new Zend_ValidateTest_False());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('validation failed'), $this->_validator->getMessages());
    }
}


class Zend_ValidateTest_True implements Zend_Validate_Interface
{
    protected $_messages = array();

    public function isValid($value)
    {
        return true;
    }

    public function getMessages()
    {
        return $this->_messages;
    }
}


class Zend_ValidateTest_False implements Zend_Validate_Interface
{
    protected $_messages = array();

    public function isValid($value)
    {
        $this->_messages = array('validation failed');
        return false;
    }

    public function getMessages()
    {
        return $this->_messages;
    }
}