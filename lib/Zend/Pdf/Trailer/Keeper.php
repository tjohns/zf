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


/** Zend_Pdf_Trailer */
require_once 'Zend/Pdf/Trailer.php';

/** Zend_Pdf_Element_Reference_Context */
require_once 'Zend/Pdf/Element/Reference/Context.php';

/**
 * PDF file trailer.
 * Stores and provides access to the trailer parced from a PDF file
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Trailer_Keeper extends Zend_Pdf_Trailer
{
    /**
     * Reference context
     *
     * @var Zend_Pdf_Element_Reference_Context
     */
    private $_context;

    /**
     * Previous trailer
     *
     * @var Zend_Pdf_Trailer
     */
    private $_prev;


    /**
     * Object constructor
     *
     * @param Zend_Pdf_Element_Dictionary $dict
     * @param Zend_Pdf_Element_Reference_Context $context
     * @param Zend_Pdf_Trailer $prev
     */
    public function __construct(Zend_Pdf_Element_Dictionary $dict,
                                Zend_Pdf_Element_Reference_Context $context,
                                $prev = null)
    {
        parent::__construct($dict);

        $this->_context = $context;
        $this->_prev    = $prev;
    }

    /**
     * Setter for $this->_prev
     *
     * @param Zend_Pdf_Trailer_Keeper $prev
     */
    public function setPrev(Zend_Pdf_Trailer_Keeper $prev)
    {
        $this->_prev = $prev;
    }

    /**
     * Getter for $this->_prev
     *
     * @return Zend_Pdf_Trailer
     */
    public function getPrev()
    {
        return $this->_prev;
    }

    /**
     * Get length of source PDF
     *
     * @return string
     */
    public function getPDFLength()
    {
        return $this->_context->getParser()->getPDFLength();
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return $this->_context->getParser()->getPDFString();
    }

    /**
     * Get reference table, which corresponds to the trailer.
     * Proxy to the $_context member methad call
     *
     * @return Zend_Pdf_Element_Reference_Context
     */
    public function getRefTable()
    {
        return $this->_context->getRefTable();
    }

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @return integer
     */
    public function getLastFreeObject()
    {
        $this->_context->getRefTable()->getNextFree('0 65535 R');
    }
}
