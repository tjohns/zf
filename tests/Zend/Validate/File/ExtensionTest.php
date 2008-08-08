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
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_Size
 */
require_once 'Zend/Validate/File/Extension.php';

/**
 * Extension testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_ExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('mo', true),
            array('gif', false),
            array(array('mo'), true),
            array(array('gif'), false),
            array(array('gif', 'pdf', 'mo', 'pict'), true),
            array(array('gif', 'gz', 'hint'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Extension($element[0]);
            $this->assertEquals($element[1], $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
        }
    }

    /**
     * Ensures that getExtension() returns expected value
     *
     * @return void
     */
    public function testGetExtension()
    {
        $validator = new Zend_Validate_File_Extension('mo');
        $this->assertEquals('mo', $validator->getExtension());

        $validator = new Zend_Validate_File_Extension(array('mo', 'gif', 'jpg'));
        $this->assertEquals('mo,gif,jpg', $validator->getExtension());

        $validator = new Zend_Validate_File_Extension(array('mo', 'gif', 'jpg'));
        $this->assertEquals(array('mo', 'gif', 'jpg'), $validator->getExtension(true));
    }

    /**
     * Ensures that setExtension() returns expected value
     *
     * @return void
     */
    public function testSetExtension()
    {
        $validator = new Zend_Validate_File_Extension('mo');
        $validator->setExtension('gif');
        $this->assertEquals('gif', $validator->getExtension());
        $this->assertEquals(array('gif'), $validator->getExtension(true));
        
        $validator->setExtension('jpg, mo');
        $this->assertEquals('jpg,mo', $validator->getExtension());
        $this->assertEquals(array('jpg', 'mo'), $validator->getExtension(true));
        
        $validator->setExtension(array('zip', 'ti'));
        $this->assertEquals('zip,ti', $validator->getExtension());
        $this->assertEquals(array('zip', 'ti'), $validator->getExtension(true));
    }

    /**
     * Ensures that addExtension() returns expected value
     *
     * @return void
     */
    public function testAddExtension()
    {
        $validator = new Zend_Validate_File_Extension('mo');
        $validator->addExtension('gif');
        $this->assertEquals('mo,gif', $validator->getExtension());
        $this->assertEquals(array('mo', 'gif'), $validator->getExtension(true));
        
        $validator->addExtension('jpg, to');
        $this->assertEquals('mo,gif,jpg,to', $validator->getExtension());
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to'), $validator->getExtension(true));
        
        $validator->addExtension(array('zip', 'ti'));
        $this->assertEquals('mo,gif,jpg,to,zip,ti', $validator->getExtension());
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getExtension(true));
    }
}
