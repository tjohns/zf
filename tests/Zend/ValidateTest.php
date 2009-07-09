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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * @see Zend_Validate
 */
require_once 'Zend/Validate.php';

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Resets the default namespaces
     *
     * @return void
     */
    public function tearDown()
    {
        Zend_Validate::setDefaultNamespaces(array());
    }

    /**
     * Ensures expected results from empty validator chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
        $this->assertTrue($this->_validator->isValid('something'));
        $this->assertEquals(array(), $this->_validator->getErrors());
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
        $this->assertEquals(array(), $this->_validator->getErrors());
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
        $this->assertEquals(array('error' => 'validation failed'), $this->_validator->getMessages());
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
        $this->assertEquals(array('error' => 'validation failed'), $this->_validator->getMessages());
    }

    /**
     * Ensures that we can call the static method is()
     * to instantiate a named validator by its class basename
     * and it returns the result of isValid() with the input.
     */
    public function testStaticFactory()
    {
        $this->assertTrue(Zend_Validate::is('1234', 'Digits'));
        $this->assertFalse(Zend_Validate::is('abc', 'Digits'));
    }

    /**
     * Ensures that a validator with constructor arguments can be called
     * with the static method is().
     */
    public function testStaticFactoryWithConstructorArguments()
    {
        $this->assertTrue(Zend_Validate::is('12', 'Between', array(1, 12)));
        $this->assertFalse(Zend_Validate::is('24', 'Between', array(1, 12)));
    }

    /**
     * Ensures that if we specify a validator class basename that doesn't
     * exist in the namespace, is() throws an exception.
     *
     * Refactored to conform with ZF-2724.
     *
     * @group  ZF-2724
     * @return void
     */
    public function testStaticFactoryClassNotFound()
    {
        set_error_handler(array($this, 'handleNotFoundError'), E_WARNING);
        try {
            Zend_Validate::is('1234', 'UnknownValidator');
        } catch (Zend_Exception $e) {
        }
        restore_error_handler();
        $this->assertTrue($this->error);
        $this->assertTrue(isset($e));
        $this->assertContains('Validate class not found', $e->getMessage());
    }

    /**
     * Handle file not found errors
     *
     * @group  ZF-2724
     * @param  int $errnum
     * @param  string $errstr
     * @return void
     */
    public function handleNotFoundError($errnum, $errstr)
    {
        if (strstr($errstr, 'No such file')) {
            $this->error = true;
        }
    }

    /**
     * Testing Namespaces
     *
     * @return void
     */
    public function testNamespaces()
    {
        $this->assertEquals(array(), Zend_Validate::getDefaultNamespaces());
        $this->assertFalse(Zend_Validate::hasDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces('TestDir');
        $this->assertEquals(array('TestDir'), Zend_Validate::getDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces('OtherTestDir');
        $this->assertEquals(array('OtherTestDir'), Zend_Validate::getDefaultNamespaces());

        $this->assertTrue(Zend_Validate::hasDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces(array());

        $this->assertEquals(array(), Zend_Validate::getDefaultNamespaces());
        $this->assertFalse(Zend_Validate::hasDefaultNamespaces());

        Zend_Validate::addDefaultNamespaces(array('One', 'Two'));
        $this->assertEquals(array('One', 'Two'), Zend_Validate::getDefaultNamespaces());

        Zend_Validate::addDefaultNamespaces('Three');
        $this->assertEquals(array('One', 'Two', 'Three'), Zend_Validate::getDefaultNamespaces());

        Zend_Validate::setDefaultNamespaces(array());
    }
}


/**
 * Validator to return true to any input.
 */
class Zend_ValidateTest_True extends Zend_Validate_Abstract
{
    public function isValid($value)
    {
        return true;
    }
}


/**
 * Validator to return false to any input.
 */
class Zend_ValidateTest_False extends Zend_Validate_Abstract
{
    public function isValid($value)
    {
        $this->_messages = array('error' => 'validation failed');
        return false;
    }
}