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

// Call Zend_Validate_File_ImageSizeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_ImageSizeTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_ImageSize
 */
require_once 'Zend/Validate/File/ImageSize.php';

/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_ImageSizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_ImageSizeTest");
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
            array(0, 10, 1000, 2000, true),
            array(0, 0, 200, 200, true),
            array(150, 150, 200, 200, false),
            array(80, 0, 80, 200, true),
            array(0, 0, 60, 200, false),
            array(90, 0, 200, 200, false),
            array(0, 0, 200, 80, false),
            array(0, 110, 200, 140, false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ImageSize($element[0], $element[1], $element[2], $element[3]);
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/picture.jpg'));
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ImageSize(array($element[0], $element[1], $element[2], $element[3]));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/picture.jpg'));
        }

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ImageSize(array('minwidth' => $element[0], 'minheight' => $element[1], 'maxwidth' => $element[2], 'maxheight' => $element[3]));
            $this->assertEquals($element[4], $validator->isValid(dirname(__FILE__) . '/_files/picture.jpg'));
        }

        $validator = new Zend_Validate_File_ImageSize(0, 10, 1000, 2000);
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/nofile.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('can not be read', $failures['fileImageSizeNotReadable']);

        $file['name'] = 'TestName';
        $validator = new Zend_Validate_File_ImageSize(0, 10, 1000, 2000);
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/nofile.jpg', $file));
        $failures = $validator->getMessages();
        $this->assertContains('TestName', $failures['fileImageSizeNotReadable']);

        $validator = new Zend_Validate_File_ImageSize(0, 10, 1000, 2000);
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/badpicture.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('could not be detected', $failures['fileImageSizeNotDetected']);
    }

    /**
     * Ensures that getImageMin() returns expected value
     *
     * @return void
     */
    public function testGetImageMin()
    {
        $validator = new Zend_Validate_File_ImageSize(1, 10, 100, 1000);
        $this->assertEquals(array(1, 10), $validator->getImageMin());

        try {
            $validator = new Zend_Validate_File_ImageSize(1000, 100, 10, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setImageMin() returns expected value
     *
     * @return void
     */
    public function testSetImageMin()
    {
        $validator = new Zend_Validate_File_ImageSize(100, 1000, 10000, 100000);
        $validator->setImageMin(10, 10);
        $this->assertEquals(array(10, 10), $validator->getImageMin());

        $validator->setImageMin(9, 100);
        $this->assertEquals(array(9, 100), $validator->getImageMin());

        try {
            $validator->setImageMin(20000, 20000);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("less than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getImageMax() returns expected value
     *
     * @return void
     */
    public function testGetImageMax()
    {
        $validator = new Zend_Validate_File_ImageSize(10, 100, 1000, 10000);
        $this->assertEquals(array(1000, 10000), $validator->getImageMax());

        try {
            $validator = new Zend_Validate_File_ImageSize(10000, 1000, 100, 10);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setImageMax() returns expected value
     *
     * @return void
     */
    public function testSetImageMax()
    {
        $validator = new Zend_Validate_File_ImageSize(10, 100, 1000, 10000);
        $validator->setImageMax(100, 100);
        $this->assertEquals(array(100, 100), $validator->getImageMax());

        $validator->setImageMax(110, 1000);
        $this->assertEquals(array(110, 1000), $validator->getImageMax());

        $validator->setImageMax(null, 1100);
        $this->assertEquals(array(null, 1100), $validator->getImageMax());

        $validator->setImageMax(120, null);
        $this->assertEquals(array(120, null), $validator->getImageMax());

        try {
            $validator->setImageMax(10000, 1);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getImageWidth() returns expected value
     *
     * @return void
     */
    public function testGetImageWidth()
    {
        $validator = new Zend_Validate_File_ImageSize(1, 10, 100, 1000);
        $this->assertEquals(array(1, 100), $validator->getImageWidth());
    }

    /**
     * Ensures that setImageWidth() returns expected value
     *
     * @return void
     */
    public function testSetImageWidth()
    {
        $validator = new Zend_Validate_File_ImageSize(100, 1000, 10000, 100000);
        $validator->setImageWidth(2000, 2200);
        $this->assertEquals(array(2000, 2200), $validator->getImageWidth());

        try {
            $validator->setImageWidth(20000, 200);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("less than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getImageHeight() returns expected value
     *
     * @return void
     */
    public function testGetImageHeight()
    {
        $validator = new Zend_Validate_File_ImageSize(1, 10, 100, 1000);
        $this->assertEquals(array(10, 1000), $validator->getImageHeight());
    }

    /**
     * Ensures that setImageHeight() returns expected value
     *
     * @return void
     */
    public function testSetImageHeight()
    {
        $validator = new Zend_Validate_File_ImageSize(100, 1000, 10000, 100000);
        $validator->setImageHeight(2000, 2200);
        $this->assertEquals(array(2000, 2200), $validator->getImageHeight());

        try {
            $validator->setImageHeight(20000, 200);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("less than or equal", $e->getMessage());
        }
    }
}
