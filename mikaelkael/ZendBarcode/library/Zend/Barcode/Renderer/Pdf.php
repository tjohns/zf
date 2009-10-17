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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
require_once 'Zend/Barcode/Renderer.php';

require_once 'Zend/Pdf.php';

require_once 'Zend/Pdf/Page.php';

require_once 'Zend/Pdf/Color/Rgb.php';

/**
 * Class for rendering the barcode in PDF resource
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Renderer_Pdf extends Zend_Barcode_Renderer
{

    /**
     * Offset of the barcode from the top border
     * @var integer
     */
    protected $_topOffset = 10;

    /**
     * Offset of the barcode from the left border
     * @var integer
     */
    protected $_leftOffset = 10;

    /**
     * PDF resource
     * @var Zend_Pdf
     */
    protected $_pdfResource = null;

    /**
     * Page number in PDF resource
     * @var integer
     */
    protected $_page = 0;

    /**
     * Draw the barcode in the PDF, send headers and the PDF
     * @return void
     */
    public function render()
    {
        $this->draw();
        header("Content-Type: application/pdf");
        echo $this->_pdfResource->render();
    }

    /**
     * Initialize the PDF resource
     * @return void
     */
    protected function _initRenderer()
    {
        if ($this->_pdfResource === null) {
            $this->_pdfResource = new Zend_Pdf();
            $this->_pdfResource->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        }
    }

    /**
     * Draw a line in the rendering resource
     * @param array $points
     * @param integer $color
     * @param float $thickness
     */
    protected function _drawLine($points, $color, $thickness = 1)
    {}

    /**
     * Draw a polygon in the rendering resource
     * @param array $points
     * @param integer $color
     * @param boolean $filled
     */
    protected function _drawPolygon ($points, $color, $filled = true)
    {
        $page = $this->_pdfResource->pages[$this->_page];
        foreach ($points as $point) {
            $x[] = $point[0] + $this->_leftOffset;
            $y[] = $page->getHeight() - $point[1] - $this->_topOffset;
        }
        if (count($y) == 4) {
            if ($x[0] != $x[3] && $y[0] == $y[3]) {
                $y[0] -= 0.5;
                $y[3] -= 0.5;
            }
            if ($x[1] != $x[2] && $y[1] == $y[2]) {
                $y[1] += 0.5;
                $y[2] += 0.5;
            }
        }
        $color = new Zend_Pdf_Color_RGB(($color & 0xFF0000) >> 16, ($color & 0x00FF00) >> 8, $color & 0x0000FF);
        $page->setLineColor($color);
        $page->setFillColor($color);
        $page->setLineWidth(1);
        $page->drawPolygon($x, $y, ($filled ? Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE : Zend_Pdf_Page::SHAPE_DRAW_STROKE));
    }

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
    protected function _drawText($text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0)
    {
        $page = $this->_pdfResource->pages[$this->_page];
        $color = new Zend_Pdf_Color_RGB(($color & 0xFF0000) >> 16, ($color & 0x00FF00) >> 8, $color & 0x0000FF);
        $page->setLineColor($color);
        $page->setFillColor($color);
        $page->setFont(Zend_Pdf_Font::fontWithPath($font), $size);
        $width = $this->widthForStringUsingFontSize($text, Zend_Pdf_Font::fontWithPath($font), $size);
        $left = $position[0] + $this->_leftOffset;
        $top = $page->getHeight() - $position[1] - $this->_topOffset;
        switch ($alignment) {
            case 'center':
                $left -= ($width / 2) * cos(pi() * $orientation / 180);
                $top -= ($width / 2) * sin(pi() * $orientation / 180);
                break;
            case 'right':
                $left -= $width;
                break;
        }
        $page->rotate($left, $top, pi() * $orientation / 180);
        $page->drawText($text, $left, $top);
        $page->rotate($left, $top, - pi() * $orientation / 180);
    }

    /**
     * Calculate the width of a string:
     * in case of using alignment parameter in drawText
     * @param string $text
     * @param Zend_Pdf_Font $font
     * @param float $fontSize
     * @return float
     */
    public function widthForStringUsingFontSize($text, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $text);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i ++) {
            $characters[] = (ord($drawingString[$i ++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;
    }
}