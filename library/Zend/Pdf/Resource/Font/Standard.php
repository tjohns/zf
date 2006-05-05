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
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $this->_resource->Encoding = new Zend_Pdf_Element_Name('WinAnsiEncoding');
    }

    /**
     * Convert string encoding from current locale to font encoding
     *
     * @param string $in
     * @return string
     */
    public function applyEncoding($in)
    {
        return iconv('', 'CP1252//IGNORE', $in);
    }
}

