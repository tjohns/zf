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

// Call Zend_Validate_File_SizeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_SizeTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_Size
 */
require_once 'Zend/Validate/File/Size.php';

/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_SizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_SizeTest");
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
            array(0, 10000, true),
            array(0, '10 MB', true),
            array(0, '10MB', true),
            array(0, '10  MB', true),
            array(794, null, true),
            array(0, 500, false),
            array(500, null, false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Size($element[0], $element[1]);
            $this->assertEquals(
                $element[2], 
                $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Size(array($element[0], $element[1]));
            $this->assertEquals(
                $element[2],
                $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Size(array('min' => $element[0], 'max' => $element[1]));
            $this->assertEquals(
                $element[2],
                $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_Size(array(2000));
        $this->assertEquals(true, $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));

        $validator = new Zend_Validate_File_Size(array(1, 2, 3));
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Size(array(2000));
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/nofile.mo', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Size(array(0, 1));
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Size(array(5000, 10000));
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo', $files));
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_Size(1, 100);
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_Size(100, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_Size(array(1, 100));
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_Size(array(100, 1));
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
        $validator = new Zend_Validate_File_Size(1000, 10000);
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
        $validator = new Zend_Validate_File_Size(1, 100);
        $this->assertEquals('100B', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_Size(100, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_Size(array(1, 100000));
        $this->assertEquals('97.66kB', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_Size(array(100, 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_Size(2000);
        $this->assertEquals('2000', $validator->getMax(false));
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_Size(null, null);
        $validator->setMax(null);
        $this->assertEquals('0B', $validator->getMax());

        $validator->setMax(1000000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMax('100 AB');
        $this->assertEquals('100B', $validator->getMax());

        $validator->setMax('100 kB');
        $this->assertEquals('100kB', $validator->getMax());

        $validator->setMax('100 MB');
        $this->assertEquals('100MB', $validator->getMax());

        $validator->setMax('1 GB');
        $this->assertEquals('1GB', $validator->getMax());

        $validator->setMax('0.001 TB');
        $this->assertEquals('1.02GB', $validator->getMax());

        $validator->setMax('0.000001 PB');
        $this->assertEquals('1.05GB', $validator->getMax());

        $validator->setMax('0.000000001 EB');
        $this->assertEquals('1.07GB', $validator->getMax());

        $validator->setMax('0.000000000001 ZB');
        $this->assertEquals('1.1GB', $validator->getMax());

        $validator->setMax('0.000000000000001 YB');
        $this->assertEquals('1.13GB', $validator->getMax());
    }
}

// Call Zend_Validate_File_SizeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_SizeTest::main") {
    Zend_Validate_File_SizeTest::main();
}
