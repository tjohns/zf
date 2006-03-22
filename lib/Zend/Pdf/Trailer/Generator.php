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
require_once 'Zend/Pdf/Const.php';

/** Zend_Pdf_Trailer */
require_once 'Zend/Pdf/Trailer.php';


/**
 * PDF file trailer generator (used for just created PDF)
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Trailer_Generator extends Zend_Pdf_Trailer
{
    /**
     * Object constructor
     *
     * @param Zend_Pdf_Element_Dictionary $dict
     */
    public function __construct(Zend_Pdf_Element_Dictionary $dict)
    {
        parent::__construct($dict);
    }

    /**
     * Get length of source PDF
     *
     * @return string
     */
    public function getPDFLength()
    {
        return strlen(Zend_Pdf_Const::PDF_HEADER);
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return Zend_Pdf_Const::PDF_HEADER;
    }

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @return integer
     */
    public function getLastFreeObject()
    {
        return 0;
    }
}
