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
 * @see Zend_Validate_Date
 */
require_once 'Zend/Validate/Date.php';


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
class Zend_Validate_DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_Date object
     *
     * @var Zend_Validate_Date
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_Date();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            '2007-01-01' => true,
            '2007-02-28' => true,
            '2007-02-29' => true,
            '2007-02-30' => true,
            '2007-02-99' => true,
            '9999-99-99' => true,
            0            => true,
            999999999999 => true,
            'Jan 1 2007' => true
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures that getFormat() returns expected default value
     *
     * @return void
     */
    public function testGetFormat()
    {
        $this->assertEquals(null, $this->_validator->getFormat());
    }

    /**
     * Ensures that getLocale() returns expected default value
     *
     * @return void
     */
    public function testGetLocale()
    {
        $this->assertTrue(is_string($this->_validator->getLocale()));
    }

    /**
     * Ensures that setLocale() follows expected behavior
     *
     * @return void
     */
    public function testSetLocale()
    {
        /**
         * @see Zend_Locale
         */
        require_once 'Zend/Locale.php';
        $this->assertTrue(is_string($this->_validator->setLocale(new Zend_Locale())->getLocale()));
        $this->assertEquals('en_US', $this->_validator->setLocale('en_US')->getLocale());
    }

    public function testInvalidDate()
    {
        $this->assertFalse($this->_validator->isValid('invalid'));
    }
}
