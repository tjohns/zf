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
 * CMYK color implementation
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Color_CMYK extends Zend_Pdf_Color
{
    /**
     * Cyan level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_c;

    /**
     * Magenta level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_m;

    /**
     * Yellow level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_y;

    /**
     * Key (BlacK) level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_k;


    /**
     * Object constructor
     *
     * @param float $c
     * @param float $m
     * @param float $y
     * @param float $k
     */
    public function __construct($c, $m, $y, $k)
    {
        $this->_c = new Zend_Pdf_Element_Numeric($c);
        $this->_m = new Zend_Pdf_Element_Numeric($m);
        $this->_y = new Zend_Pdf_Element_Numeric($y);
        $this->_k = new Zend_Pdf_Element_Numeric($k);

        if ($this->_c->value < 0) { $this->_c->value = 0; }
        if ($this->_c->value > 1) { $this->_c->value = 1; }

        if ($this->_m->value < 0) { $this->_m->value = 0; }
        if ($this->_m->value > 1) { $this->_m->value = 1; }

        if ($this->_y->value < 0) { $this->_y->value = 0; }
        if ($this->_y->value > 1) { $this->_y->value = 1; }

        if ($this->_k->value < 0) { $this->_k->value = 0; }
        if ($this->_k->value > 1) { $this->_k->value = 1; }
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
        return $this->_c->toString() . ' '
             . $this->_m->toString() . ' '
             . $this->_y->toString() . ' '
             . $this->_k->toString() .     ($stroking? " K\n" : " k\n");
    }
}

