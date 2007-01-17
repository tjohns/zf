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
 * @see Zend_Validate_StringLength
 */
require_once 'Zend/Validate/StringLength.php';


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
class Zend_Validate_StringLengthTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        /**
         * The elements of each array are, in order:
         *      - minimum length
         *      - maximum length
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array(0, null, true, array('', 'a', 'ab')),
            array(-1, null, true, array('')),
            array(2, 2, true, array('ab', '  ')),
            array(2, 2, false, array('a', 'abc')),
            array(1, null, false, array('')),
            array(2, 3, true, array('ab', 'abc')),
            array(2, 3, false, array('a', 'abcd'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_StringLength($element[0], $element[1]);
            foreach ($element[3] as $input) {
                $this->assertEquals($element[2], $validator->isValid($input));
            }
        }
    }
}
