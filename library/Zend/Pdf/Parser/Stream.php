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


/** Zend_Pdf_Parser */
require_once 'Zend/Pdf/Parser.php';



/**
 * PDF object stream parser
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Parser_Stream extends Zend_Pdf_Parser
{
    /**
     * Get Trailer object
     *
     * @return Zend_Pdf_Trailer_Keeper
     */
    public function getTrailer()
    {
        throw new Zend_Pdf_Exception('Stream object parser doesn\'t contain trailer information.');
    }

    /**
     * Object constructor
     *
     * @param string $pdfString
     * @param Zend_Pdf_ElementFactory $factory
     * @throws Zend_Exception
     */
    public function __construct(&$source, Zend_Pdf_ElementFactory $factory)
    {
        $this->_current        = 0;
        $this->_currentContext = null;
        $this->_contextStack   = array();
        $this->_elements       = new Zend_Pdf_PHPArray();
        $this->_objFactory     = $factory;
    }
}
