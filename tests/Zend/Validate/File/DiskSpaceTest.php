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
require_once 'Zend/Validate/File/DiskSpace.php';

/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_DiskSpaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(0, 2000, true, true, false),
            array(0, '2 MB', true, true, true),
            array(0, '2MB', true, true, true),
            array(0, '2  MB', true, true, true),
            array(2000, 0, true, true, false),
            array(0, 500, false, false, false),
            array(500, 0, false, false, false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_DiskSpace($element[0], $element[1]);
            $this->assertEquals($element[2], $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
            $this->assertEquals($element[3], $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/testsize3.mo'));
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_DiskSpace(array($element[0], $element[1]));
            $this->assertEquals($element[2], $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
            $this->assertEquals($element[3], $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/testsize3.mo'));
        }
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_DiskSpace(1, 100);
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_DiskSpace(100, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_DiskSpace(array(1, 100));
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_DiskSpace(array(100, 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setMin() returns expected value
     *
     * @return void
     */
    public function testSetMin()
    {
        $validator = new Zend_Validate_File_DiskSpace(1000, 10000);
        $validator->setMin(100);
        $this->assertEquals('100B', $validator->getMin());

        try {
            $validator->setMin(20000);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("less than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new Zend_Validate_File_DiskSpace(1, 100);
        $this->assertEquals('100B', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_DiskSpace(100, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_DiskSpace(array(1, 100000));
        $this->assertEquals('97.66kB', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_DiskSpace(array(100, 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_DiskSpace(2000);
        $this->assertEquals('2000', $validator->getMax(false));
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_DiskSpace(1000, 10000);
        $validator->setMax(1000000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());
    }
}
