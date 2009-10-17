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
 * @package    Zend_Image
 * @subpackage Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Class for rendering the barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Barcode_Renderer
{

    /**
     * Offset of the barcode from the top of the rendering resource
     * @var integer
     */
    protected $_topOffset = 0;

    /**
     * Offset of the barcode from the left of the rendering resource
     * @var integer
     */
    protected $_leftOffset = 0;

    /**
     * Barcode object
     * @var Zend_Barcode_Object
     */
    protected $_barcode;

    /**
     * Set the barcode object
     * @param Zend_Barcode_Object $barcode
     * @return Zend_Barcode_Renderer
     */
    public function setBarcode($barcode)
    {
        $this->_barcode = $barcode;
        return $this;
    }

    /**
     * Retrieve the barcode object
     * @return Zend_Barcode_Object
     */
    public function getBarcode()
    {
        return $this->_barcode;
    }

    /**
     * Draw the barcode in the rendering resource
     * @return void
     */
    public function draw()
    {
        $this->_initRenderer();

        $instructionList = $this->_barcode->draw();
        //Zend_Debug::dump($instructionList);
        foreach ($instructionList as $instruction) {
            switch ($instruction['type']) {
                case 'line':
                    $this->_drawLine($instruction['points'],
                        $instruction['color'],
                        $instruction['thickness']);
                    break;
                case 'polygon':
                    $this->_drawPolygon($instruction['points'],
                        $instruction['color'],
                        $instruction['filled']);
                    break;
                case 'text': //$text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0)
                    $this->_drawText($instruction['text'],
                        $instruction['size'],
                        $instruction['position'],
                        $instruction['font'],
                        $instruction['color'],
                        $instruction['alignment'],
                        $instruction['orientation']);
                    break;
                default:
                    throw new Exception('Unkown drawing command');
            }
        }
    }

    /**
     * Render the resource by sending headers and drawed resource
     * @return void
     */
    abstract public function render();

    /**
     * Initialize the rendering resource
     * @return void
     */
    abstract protected function _initRenderer();

    /**
     * Draw a line in the rendering resource
     * @param array $points
     * @param integer $color
     * @param float $thickness
     */
    abstract protected function _drawLine($points, $color, $thickness = 1);

    /**
     * Draw a polygon in the rendering resource
     * @param array $points
     * @param integer $color
     * @param boolean $filled
     */
    abstract protected function _drawPolygon($points, $color, $filled = true);

    /**
     * Draw a polygon in the rendering resource
     * @param string $text
     * @param float $size
     * @param array $position
     * @param string $font
     * @param integer $color
     * @param string $alignment
     * @param float $orientation
     */
    abstract protected function _drawText($text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0);
}