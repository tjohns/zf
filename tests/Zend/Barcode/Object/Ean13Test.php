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
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';

require_once dirname(__FILE__) . '/TestCommon.php';

require_once 'Zend/Barcode/Object/Ean13.php';

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_Ean13Test extends Zend_Barcode_Object_TestCommon
{

    protected function _getBarcodeObject($options = null)
    {
        return new Zend_Barcode_Object_Ean13($options);
    }

    public function testType()
    {
        $this->assertSame('ean13', $this->_object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame(2, $this->_object->getChecksum('012345678901'));
    }

    public function testSetText()
    {
        $this->_object->setText('000123456789');
        $this->assertSame('000123456789', $this->_object->getRawText());
        $this->assertSame('0001234567895', $this->_object->getText());
        $this->assertSame('0001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithout13Characters()
    {
        $this->_object->setText('123456789');
        $this->assertSame('123456789', $this->_object->getRawText());
        $this->assertSame('0001234567895', $this->_object->getText());
        $this->assertSame('0001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->_object->setText('000123456789');
        $this->_object->setWithChecksum(false);
        $this->assertSame('000123456789', $this->_object->getRawText());
        $this->assertSame('0001234567895', $this->_object->getText());
        $this->assertSame('0001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->_object->setText(' 000123456789 ');
        $this->assertSame('000123456789', $this->_object->getRawText());
        $this->assertSame('0001234567895', $this->_object->getText());
        $this->assertSame('0001234567895', $this->_object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->_object->setText('000123456789');
        $this->_object->setWithChecksumInText(false);
        $this->assertSame('000123456789', $this->_object->getRawText());
        $this->assertSame('0001234567895', $this->_object->getText());
        $this->assertSame('0001234567895', $this->_object->getTextToDisplay());
    }

    /**
     * @expectedException Zend_Barcode_Object_Exception
     */
    public function testBadTextDetectedIfChecksumWished()
    {
        $this->_object->setText('a');
        $this->_object->setWithChecksum(true);
        $this->_object->getText();
    }

    public function testCheckGoodParams()
    {
        $this->_object->setText('000123456789');
        $this->assertTrue($this->_object->checkParams());
    }


    public function testGetKnownWidthWithoutOrientation()
    {
        $this->_object->setText('000123456789');
        $this->assertEquals(115, $this->_object->getWidth());
        $this->_object->setWithQuietZones(false);
        $this->assertEquals(115, $this->_object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->_object->setText('000123456789');
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile('Ean13_000123456789_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->_object->setText('000123456789');
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean13_000123456789_border_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->_object->setText('000123456789');
        $this->_object->setOrientation(60);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean13_000123456789_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->_object->setText('000123456789');
        $this->_object->setOrientation(60);
        $this->_object->setWithBorder(true);
        $this->_object->draw();
        $instructions = $this->loadInstructionsFile(
                'Ean13_000123456789_border_oriented_instructions');
        $this->assertEquals($instructions, $this->_object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->_object->setText('000123456789');
        $this->assertEquals(62, $this->_object->getHeight(true));
    }
}
