<?php
/**
 * @package Zend_Pdf
 */

/**
 * Zend_Pdf_Font
 */
require_once 'Zend/Pdf/Resource/Font.php';

/**
 * Zend_Pdf_Const
 */
require_once 'Zend/Pdf/Const.php';

/**
 * Zend_Pdf_Exception
 */
require_once 'Zend/Pdf/Exception.php';

/**
 * Type1 standard fonts implementation
 *
 * @package Zend_Pdf
 */
class Zend_Pdf_Font_Standard extends Zend_Pdf_Font
{
    /**
     * Object constructor
     *
     * @param string $fontName
     * @throws Zend_Pdf_Exception
     */
    public function __construct($fontName)
    {
        if (!in_array($fontName, Zend_Pdf_Const::$standardFonts)) {
            throw new Zend_Pdf_Exception('Wrong font name');
        }

        parent::__construct();

        $this->_resource->Subtype  = new Zend_Pdf_Element_Name('Type1');
        $this->_resource->BaseFont = new Zend_Pdf_Element_Name($fontName);
    }
}

