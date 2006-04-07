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

/** Zend_Pdf_Element_Reference_Table */
require_once 'Zend/Pdf/Element/Reference/Table.php';


/**
 * PDF reference object context
 * Reference context is defined by PDF parser and PDF Refernce table
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Element_Reference_Context
{
    /**
     * PDF parser object.
     *
     * @var Zend_Pdf_Parser
     */
    private $_stringParser;

    /**
     * Reference table
     *
     * @var Zend_Pdf_Element_Reference_Table
     */
    private $_refTable;

    /**
     * Object constructor
     *
     * @param Zend_Pdf_StringParser $parser
     * @param Zend_Pdf_Element_Reference_Table $refTable
     */
    public function __construct(Zend_Pdf_StringParser $parser,
                                Zend_Pdf_Element_Reference_Table $refTable)
    {
        $this->_stringParser = $parser;
        $this->_refTable     = $refTable;
    }


    /**
     * Context parser
     *
     * @return Zend_Pdf_Parser
     */
    public function getParser()
    {
        return $this->_stringParser;
    }


    /**
     * Context reference table
     *
     * @return Zend_Pdf_Element_Reference_Table
     */
    public function getRefTable()
    {
        return $this->_refTable;
    }
}

