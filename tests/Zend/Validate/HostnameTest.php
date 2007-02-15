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
 * Temp setup to enable PHPUnit 3 usage for on SRJ Sony Vaio
 */
set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\Subversion\ZendFramework-trunk\library');
class HostnameTest extends Zend_Validate_HostnameTest {}

/**
 * @see Zend_Validate_Hostname
 */
require_once 'Zend/Validate/Hostname.php';


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
class Zend_Validate_HostnameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validate_Hostname
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Hostname object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_Hostname();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_IP, true, array('1.2.3.4', '10.0.0.1', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_IP, false, array('0.0.0.0', '0.0.0.256')),
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('example.com', 'example.museum', 'd.hatena.ne.jp')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('localhost', 'localhost.localdomain', '1.2.3.4', 'domain.invalid')),
            array(Zend_Validate_Hostname::ALLOW_LOCAL, true, array('localhost', 'localhost.localdomain', 'example.com')),
            array(Zend_Validate_Hostname::ALLOW_ALL, true, array('localhost', 'example.com', '1.2.3.4'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }
    
    public function testCombination()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_LOCAL, true, array('domain.com', 'localhost', 'local.localhost')),
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_LOCAL, false, array('1.2.3.4', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_IP, true, array('1.2.3.4', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_IP, false, array('localhost', 'local.localhost'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

    /**
     * Ensure the dash character tests work as expected
     *
     */
    public function testDashes()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('domain.com', 'doma-in.com')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('-domain.com', 'domain-.com', 'do--main.com'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
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
     * Ensures that getAllow() returns expected default value
     *
     * @return void
     */
    public function testGetAllow()
    {
        $this->assertEquals(Zend_Validate_Hostname::ALLOW_DNS, $this->_validator->getAllow());
    }

    /**
     * Ensures that getRegex() returns expected default values and throws an exception for unknown type
     *
     * @return void
     */
    public function testGetRegex()
    {
        $this->assertEquals(Zend_Validate_Hostname::REGEX_LOCAL_DEFAULT, $this->_validator->getRegex('local'));
        try {
            $this->_validator->getRegex('does not exist');
            $this->fail('Expected Zend_Validate_Exception not thrown for unknown regex type');
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains('must be one of', $e->getMessage());
        }
    }

    /**
     * Ensures that an exception is thrown when a bad local regex is supplied
     *
     * @return void
     */
    public function testBadRegexLocal()
    {
        try {
            $this->_validator->setRegex('local', '/')->isValid('anything');
            $this->fail('Expected Zend_Validate_Exception not thrown for bad local network name regex');
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains('local network name validation failed', $e->getMessage());
        }
    }
}
