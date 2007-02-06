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
 * @see Zend_Validate_EmailAddress
 */
require_once 'Zend/Validate/EmailAddress.php';


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
class Zend_Validate_EmailAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validate_EmailAddress
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_EmailAddress object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_EmailAddress();
    }

    /**
     * Ensures that a basic valid e-mail address passes validation
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertTrue($this->_validator->isValid('username@example.com'));
    }

    /**
     * Ensures that localhost address is valid by default
     *
     * @return void
     */
    public function testLocalhostAllowed()
    {
        $this->assertTrue($this->_validator->isValid('username@localhost'));
    }

    /**
     * Ensures that local domain names are valid by default
     *
     * @return void
     */
    public function testLocaldomainAllowed()
    {
        $this->assertTrue($this->_validator->isValid('username@localhost.localdomain'));
    }

    /**
     * Ensures that validation fails when the local part is missing
     *
     * @return void
     */
    public function testLocalPartMissing()
    {
        $this->assertFalse($this->_validator->isValid('@example.com'));
        $messages = $this->_validator->getMessages();
        $this->assertTrue(isset($messages[0]));
        $this->assertContains('local-part@hostname', $messages[0]);
    }

    /**
     * Ensures that validation fails when the hostname is invalid
     *
     * @return void
     */
    public function testHostnameInvalid()
    {
        $this->assertFalse($this->_validator->isValid('username@ example . com'));
        $messages = $this->_validator->getMessages();
        $this->assertTrue(isset($messages[0]));
        $this->assertContains('not a valid hostname', $messages[0]);
    }

    /**
     * Ensures that quoted-string local part is considered valid
     *
     * @return void
     */
    public function testQuotedString()
    {
        $this->assertTrue($this->_validator->isValid('"username"@example.com'));
    }

    /**
     * Ensures that validation fails when the e-mail is given as for display,
     * with angle brackets around the actual address
     *
     * @return void
     */
    public function testEmailDisplay()
    {
        $this->assertFalse($this->_validator->isValid('User Name <username@example.com>'));
        $messages = $this->_validator->getMessages();
        $this->assertTrue(isset($messages[0]));
        $this->assertContains('not a valid hostname', $messages[0]);
        $this->assertTrue(isset($messages[1]));
        $this->assertContains('not appear to be a valid local network name', $messages[1]);
        $this->assertTrue(isset($messages[2]));
        $this->assertContains('not a valid local part', $messages[2]);
    }
}
