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
require_once 'Zend/Barcode/Renderer.php';

/**
 * Class for generate Barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Renderer_Image extends Zend_Barcode_Renderer
{

    /**
     * List of authorized output format
     * @var array
     */
    protected $_allowedImageType = array('png' , 'jpeg' , 'gif');

    /**
     * Image format
     * @var string
     */
    protected $_imageType = 'png';

    /**
     * Resource for the image
     * @var resource
     */
    protected $_imageResource = null;

    /**
     * Resource for the font and bars color of the image
     * @var integer
     */
    protected $_imageForeColor = null;

    /**
     * Resource for the background color of the image
     * @var integer
     */
    protected $_imageBackgroundColor = null;

    /**
     * Set an image resource to draw the barcode inside
     * @param resource $value
     * @return Zend_Barcode_Renderer
     * @throw Zend_Barcode_Renderer_Exception
     */
    public function setImageResource($value)
    {
        if (gettype($value) == 'resource' && get_resource_type($value) == 'gd') {
            $this->_img_res = $value;
        } else {
            require_once 'Zend/Barcode/Renderer/Exception.php';
            throw new Zend_Barcode_Renderer_Exception('Invalid image resource provided to setImageResource()');
        }
        return $this;
    }

    /**
     * Initialize the image resource
     * @return void
     */
    protected function _initRenderer()
    {
        if ($this->_imageResource !== null) {
            $foreColor = $this->_barcode->getForeColor();
            $backgroundColor = $this->_barcode->getBackgroundColor();
            $this->_imageBackgroundColor = imagecolorallocate($this->_imageResource,
                ($backgroundColor & 0xFF0000) >> 16,
                ($backgroundColor & 0x00FF00) >> 8,
                $backgroundColor & 0x0000FF);
            $this->_imageForeColor = imagecolorallocate($this->_imageResource,
                ($foreColor & 0xFF0000) >> 16,
                ($foreColor & 0x00FF00) >> 8,
                $foreColor & 0x0000FF);
        } else {
            $width = $this->_barcode->getWidth(true);
            $height = $this->_barcode->getHeight(true);
            $foreColor = $this->_barcode->getForeColor();
            $backgroundColor = $this->_barcode->getBackgroundColor();
            $this->_imageResource = imagecreatetruecolor($width, $height);
            $this->_imageBackgroundColor = imagecolorallocate($this->_imageResource,
                ($backgroundColor & 0xFF0000) >> 16,
                ($backgroundColor & 0x00FF00) >> 8,
                $backgroundColor & 0x0000FF);
            $this->_imageForeColor = imagecolorallocate($this->_imageResource,
                ($foreColor & 0xFF0000) >> 16,
                ($foreColor & 0x00FF00) >> 8,
                $foreColor & 0x0000FF);
            imagefilledrectangle($this->_imageResource,
                0,
                0,
                $width - 1,
                $height - 1,
                $this->_imageBackgroundColor);
        }

        imageantialias($this->_imageResource, true);
    }

    /**
     * Drawing and render the barcode with correct headers
     * @return void
     */
    public function render()
    {
        $this->draw();
        header("Content-Type: image/" . $this->_imageType);
        $functionName = 'image' . $this->_imageType;
        call_user_func($functionName, $this->_imageResource);
        @imagedestroy($this->_imageResource);
    }

    /**
     * Draw a line in the image resource
     * @param array $points
     * @param integer $color
     * @param float $thickness
     */
    protected function _drawLine($points, $color, $thickness = 1)
    {
        $allocatedColor = imagecolorallocate($this->_imageResource,
            ($color & 0xFF0000) >> 16,
            ($color & 0x00FF00) >> 8,
            $color & 0x0000FF);
        if ($thickness == 1) {
            imageline($this->_imageResource,
                $points[0][0],
                $points[0][1],
                $points[1][0],
                $points[1][1],
                $allocatedColor);
            return;
        }
        $t = $thickness / 2 - 0.5;
        $x1 = $points[0][0];
        $y1 = $points[0][1];
        $x2 = $points[1][0];
        $y2 = $points[1][1];
        if ($x1 == $x2 || $y1 == $y2) {
            imagefilledrectangle($this->_imageResource,
                round(min($x1, $x2) - $t),
                round(min($y1, $y2) - $t),
                round(max($x1, $x2) + $t),
                round(max($y1, $y2) + $t),
                $color);
            return;
        }
        $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
        $a = $t / sqrt(1 + pow($k, 2));
        $points = array(round($x1 - (1 + $k) * $a) ,
            round($y1 + (1 - $k) * $a) ,
            round($x1 - (1 - $k) * $a) ,
            round($y1 - (1 + $k) * $a) ,
            round($x2 + (1 + $k) * $a) ,
            round($y2 - (1 - $k) * $a) ,
            round($x2 + (1 - $k) * $a) ,
            round($y2 + (1 + $k) * $a));
        imagefilledpolygon($this->_imageResource, $points, 4, $color);
    }

    /**
     * Draw a polygon in the image resource
     * @param array $points
     * @param integer $color
     * @param boolean $filled
     */
    protected function _drawPolygon($points, $color, $filled = true)
    {
        $newPoints = array($points[0][0] + $this->_leftOffset ,
            $points[0][1] + $this->_topOffset ,
            $points[1][0] + $this->_leftOffset ,
            $points[1][1] + $this->_topOffset ,
            $points[2][0] + $this->_leftOffset ,
            $points[2][1] + $this->_topOffset ,
            $points[3][0] + $this->_leftOffset ,
            $points[3][1] + $this->_topOffset);
        $allocatedColor = imagecolorallocate($this->_imageResource,
            ($color & 0xFF0000) >> 16,
            ($color & 0x00FF00) >> 8,
            $color & 0x0000FF);
        if ($filled) {
            imagefilledpolygon($this->_imageResource, $newPoints, 4, $allocatedColor);
        } else {
            imagepolygon($this->_imageResource, $newPoints, 4, $allocatedColor);
        }
    }

    /**
     * Draw a polygon in the image resource
     * @param string $text
     * @param float $size
     * @param array $position
     * @param string $font
     * @param integer $color
     * @param string $alignment
     * @param float $orientation
     */
    protected function _drawText($text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0)
    {
        $allocatedColor = imagecolorallocate($this->_imageResource,
            ($color & 0xFF0000) >> 16,
            ($color & 0x00FF00) >> 8,
            $color & 0x0000FF);
        $box = imagettfbbox($size, $orientation, $font, $text);
        switch ($alignment) {
            case 'left':
                $width = 0;
                break;
            case 'center':
                $width = ($box[2] - $box[0]) / 2;
                break;
            case 'right':
                $width = ($box[2] - $box[0]);
                break;
        }
        imagettftext($this->_imageResource,
            $size,
            $orientation,
            $position[0] - ($width * cos(pi() * $orientation / 180)),
            $position[1] + ($width * sin(pi() * $orientation / 180)),
            $allocatedColor,
            $font,
            $text);
    }
}