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
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_IP, true, array('1.2.3.4', '10.0.0.1', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_IP, false, array('0.0.0.0', '0.0.0.256')),
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('example.com', 'example.museum')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('localhost', 'localhost.localdomain', '1.2.3.4')),
            array(Zend_Validate_Hostname::ALLOW_LOCAL, true, array('localhost', 'localhost.localdomain', 'example.com')),
            array(Zend_Validate_Hostname::ALLOW_ALL, true, array('localhost', 'example.com', '1.2.3.4'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input));
            }
        }
    }
}
