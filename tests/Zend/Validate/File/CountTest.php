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

// Call Zend_Validate_File_CountTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_CountTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_Count
 */
require_once 'Zend/Validate/File/Count.php';

/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_CountTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_CountTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(5, null, true, true, true, true),
            array(0, 3, true, true, true, false),
            array(2, 3, false, true, true, false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Count($element[0], $element[1]);
            $this->assertEquals($element[2], $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
            $this->assertEquals($element[3], $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/testsize3.mo'));
            $this->assertEquals($element[5], $validator->isValid(dirname(__FILE__) . '/_files/testsize4.mo'));
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Count(array($element[0], $element[1]));
            $this->assertEquals($element[2], $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
            $this->assertEquals($element[3], $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/testsize3.mo'));
            $this->assertEquals($element[5], $validator->isValid(dirname(__FILE__) . '/_files/testsize4.mo'));
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Count(array('min' => $element[0], 'max' => $element[1]));
            $this->assertEquals($element[2], $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
            $this->assertEquals($element[3], $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/testsize3.mo'));
            $this->assertEquals($element[5], $validator->isValid(dirname(__FILE__) . '/_files/testsize4.mo'));
        }

        $validator = new Zend_Validate_File_Count(array(1));
        $this->assertEquals(true, $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'));
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_Count(1, 5);
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new Zend_Validate_File_Count(5, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_Count(array(1, 5));
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new Zend_Validate_File_Count(array(5, 1));
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
        $validator = new Zend_Validate_File_Count(1000, 10000);
        $validator->setMin(100);
        $this->assertEquals(100, $validator->getMin());

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
        $validator = new Zend_Validate_File_Count(1, 100);
        $this->assertEquals(100, $validator->getMax());

        try {
            $validator = new Zend_Validate_File_Count(5, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_Count(array(1, 50));
        $this->assertEquals(50, $validator->getMax());

        try {
            $validator = new Zend_Validate_File_Count(array(50, 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_Count(1000, 10000);
        $validator->setMax(1000000);
        $this->assertEquals(1000000, $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals(1000000, $validator->getMax());

        $validator->setMax(null);
        $this->assertEquals(null, $validator->getMax());
    }
}

// Call Zend_Validate_File_CountTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_CountTest::main") {
    Zend_Validate_File_CountTest::main();
}
