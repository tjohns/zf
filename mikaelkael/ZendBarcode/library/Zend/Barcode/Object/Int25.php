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
 * @subpackage Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
require_once 'Zend/Barcode/Object.php';

/**
 * Class for generate Barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_Int25 extends Zend_Barcode_Object
{

    /**
     * Coding map
     * 0 = narrow bar
     * 1 = wide bar
     * @var array
     */
    private $_codingMap = array('0' => '00110' ,
        '1' => '10001' ,
        '2' => '01001' ,
        '3' => '11000' ,
        '4' => '00101' ,
        '5' => '10100' ,
        '6' => '01100' ,
        '7' => '00011' ,
        '8' => '10010' ,
        '9' => '01010');

    /**
     * Drawing of bearer bars
     * @var boolean
     */
    private $_withBearerBars = false;

    /**
     * Activate/deactivate drawing of bearer bars
     * @param boolean $value
     * @return Zend_Barcode_Object_Int25
     */
    public function setWithBearerBars($value)
    {
        $this->_withBearerBars = (bool) $value;
        return $this;
    }

    /**
     * Retrieve if bearer bars are enabled
     * @return boolean
     */
    public function getWithBearerBars()
    {
        return $this->_withBearerBars;
    }

    /**
     * Check allowed characters
     * @param string $value
     * @return string
     * @throw Zend_Barcode_Object_Exception
     */
    public function validateText($value)
    {
        if (! preg_match("/^[0-9]*$/", $value)) {
            require_once 'Zend/Barcode/Object/Exception.php';
            throw new Zend_Barcode_Object_Exception('Interleaved 2 of 5 just allow numerics characters');
        }
    }

    /**
     * Set text to encode
     * @param string $value
     * @return Zend_Barcode_Object_Int25
     */
    public function setText($value)
    {
        return parent::setText(strlen($value) % 2 ? '0' . $value : $value);
    }

    /**
     * Calculate the width of a character
     * @return integer
     */
    protected function _characterLength()
    {
        return (3 * $this->_barThinWidth + 2 * $this->_barThickWidth) * $this->_factor;
    }

    /**
     * Width of the barcode (in pixels)
     * @return integer
     */
    protected function _calculateBarcodeWidth()
    {
        $quietzone = $this->getQuietZone();
        $start_character = (4 * $this->_barThinWidth) * $this->_factor;
        $encoded_data = strlen($this->_text) * $this->_characterLength();
        $stop_character = ($this->_barThickWidth + 2 * $this->_barThinWidth) * $this->_factor;
        return $quietzone + $start_character + $encoded_data + $stop_character + $quietzone;
    }

    /**
     * Partial check of interleaved 2 of 5 barcode
     * @return void
     */
    protected function _checkParams()
    {
        $this->_checkRatio();
    }

    /**
     * Prepare array to draw barcode
     * @return array
     */
    protected function _prepareBarcode()
    {
        if ($this->_withBearerBars) {
            $this->_withBorder = false;
        }

        // Quiet zone
        // not implemented
        // Start character (0000)
        $barcodeTable[] = array(
            1 ,
            $this->_barThinWidth ,
            0 ,
            1);
        $barcodeTable[] = array(0 , $this->_barThinWidth , 0 , 1);
        $barcodeTable[] = array(1 , $this->_barThinWidth , 0 , 1);
        $barcodeTable[] = array(0 , $this->_barThinWidth , 0 , 1);
        // Encoded $text
        for ($i = 0; $i < strlen($this->_text); $i += 2) { // Draw 2 chars at a time
            $char1 = substr($this->_text, $i, 1);
            $char2 = substr($this->_text, $i + 1, 1);
            // Interleave
            for ($ibar = 0; $ibar < 5; $ibar ++) {
                // Draws char1 bar (fore color)
                $bar_width = ((substr(
                    $this->_codingMap[$char1],
                    $ibar,
                    1)) ? $this->_barThickWidth : $this->_barThinWidth);
                $barcodeTable[] = array(1 , $bar_width , 0 , 1);
                // Left space corresponding to char2 (background color)
                $bar_width = ((substr(
                    $this->_codingMap[$char2],
                    $ibar,
                    1)) ? $this->_barThickWidth : $this->_barThinWidth);
                $barcodeTable[] = array(0 , $bar_width , 0 , 1);
            }
        }
        // Stop character (100)
        $barcodeTable[] = array(1 , $this->_barThickWidth , 0 , 1);
        $barcodeTable[] = array(0 , $this->_barThinWidth , 0 , 1);
        $barcodeTable[] = array(1 , $this->_barThinWidth , 0 , 1);
        return $barcodeTable;
    }

    /**
     * Drawing of bearer bars (if enabled)
     * @return void
     */
    protected function _postDrawBarcode()
    {
        if ($this->_withBearerBars) {
            $width = $this->_barThickWidth * $this->_factor;
            $point1 = $this->_rotate(1, 1);
            $point2 = $this->_rotate($this->_calculateWidth() - 1, 1);
            $point3 = $this->_rotate($this->_calculateWidth() - 1, $width);
            $point4 = $this->_rotate(1, $width);
            $this->addPolygon(array($point1 , $point2 , $point3 , $point4));
            $point1 = $this->_rotate(1, 1 + $this->_barHeight * $this->_factor);
            $point2 = $this->_rotate($this->_calculateWidth() - 1, 1 + $this->_barHeight * $this->_factor);
            $point3 = $this->_rotate($this->_calculateWidth() - 1, 2 + $this->_barHeight * $this->_factor - $width);
            $point4 = $this->_rotate(1, 2 + $this->_barHeight * $this->_factor - $width);
            $this->addPolygon(array($point1 , $point2 , $point3 , $point4));
        }
    }
}