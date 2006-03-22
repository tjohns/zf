<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Pdf_Exception */
require_once 'Zend/Pdf/Exception.php';

/** Zend_Pdf_Color */
require_once 'Zend/Pdf/Color.php';

/** Zend_Pdf_Element_Numeric */
require_once 'Zend/Pdf/Element/Numeric.php';

/**
 * RGB color implementation
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Color_RGB extends Zend_Pdf_Color
{
    /**
     * Red level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_r;

    /**
     * Green level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_g;

    /**
     * Blue level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_b;


    /**
     * Object constructor
     *
     * @param float $r
     * @param float $g
     * @param float $b
     */
    public function __construct($r, $g, $b)
    {
        $this->_r = new Zend_Pdf_Element_Numeric($r);
        $this->_g = new Zend_Pdf_Element_Numeric($g);
        $this->_b = new Zend_Pdf_Element_Numeric($b);

        if ($this->_r->value < 0) { $this->_r->value = 0; }
        if ($this->_r->value > 1) { $this->_r->value = 1; }

        if ($this->_g->value < 0) { $this->_g->value = 0; }
        if ($this->_g->value > 1) { $this->_g->value = 1; }

        if ($this->_b->value < 0) { $this->_b->value = 0; }
        if ($this->_b->value > 1) { $this->_b->value = 1; }
    }

    /**
     * Instructions, which can be directly inserted into content stream
     * to switch color.
     * Color set instructions differ for stroking and nonstroking operations.
     *
     * @param boolean $stroking
     * @return string
     */
    public function instructions($stroking)
    {
        return $this->_r->toString() . ' '
             . $this->_g->toString() . ' '
             . $this->_b->toString() .     ($stroking? " RG\n" : " rg\n");
    }
}

