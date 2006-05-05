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
 * Module constants
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Const
{
    // Supported pdf version
    const PDF_VERSION = 1.4;
    const PDF_HEADER  = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";

    // Page sizes
    const PAGESIZE_A4                = '595:842:';
    const PAGESIZE_A4_LANDSCAPE      = '842:595:';
    const PAGESIZE_LETTER            = '612:792:';
    const PAGESIZE_LETTER_LANDSCAPE  = '792:612:';

    static public $pageSizeAliases = array( 'A4'               => Zend_Pdf_Const::PAGESIZE_A4,
                                            'A4-landscape'     => Zend_Pdf_Const::PAGESIZE_A4_LANDSCAPE,
                                            'A4-Landscape'     => Zend_Pdf_Const::PAGESIZE_A4_LANDSCAPE,
                                            'A4-LANDSCAPE'     => Zend_Pdf_Const::PAGESIZE_A4_LANDSCAPE,
                                            'Letter'           => Zend_Pdf_Const::PAGESIZE_LETTER,
                                            'LETTER'           => Zend_Pdf_Const::PAGESIZE_LETTER,
                                            'Letter-landscape' => Zend_Pdf_Const::PAGESIZE_LETTER_LANDSCAPE,
                                            'Letter-Landscape' => Zend_Pdf_Const::PAGESIZE_LETTER_LANDSCAPE,
                                            'Letter-LANDSCAPE' => Zend_Pdf_Const::PAGESIZE_LETTER_LANDSCAPE,
                                            'LETTER-landscape' => Zend_Pdf_Const::PAGESIZE_LETTER_LANDSCAPE,
                                            'LETTER-Landscape' => Zend_Pdf_Const::PAGESIZE_LETTER_LANDSCAPE,
                                            'LETTER-LANDSCAPE' => Zend_Pdf_Const::PAGESIZE_LETTER_LANDSCAPE,
                                          );

    // Type1 standard fonts
    const FONT_TIMES_ROMAN          = 'Times-Roman';
    const FONT_TIMES_BOLD           = 'Times-Bold';
    const FONT_TIMES_ITALIC         = 'Times-Italic';
    const FONT_TIMES_BOLDITALIC     = 'Times-BoldItalic';
    const FONT_HELVETICA            = 'Helvetica';
    const FONT_HELVETICA_BOLD       = 'Helvetica-Bold';
    const FONT_HELVETICA_ITALIC     = 'Helvetica-Oblique';
    const FONT_HELVETICA_BOLDITALIC = 'Helvetica-BoldOblique';
    const FONT_COURIER              = 'Courier';
    const FONT_COURIER_BOLD         = 'Courier-Bold';
    const FONT_COURIER_ITALIC       = 'Courier-Oblique';
    const FONT_COURIER_BOLDITALIC   = 'Courier-BoldOblique';
    const FONT_SYMBOL               = 'Symbol';
    const FONT_ZAPFDINGBATS         = 'ZapfDingbats';

    static public $standardFonts = array(Zend_Pdf_Const::FONT_TIMES_ROMAN,
                                         Zend_Pdf_Const::FONT_TIMES_BOLD,
                                         Zend_Pdf_Const::FONT_TIMES_ITALIC,
                                         Zend_Pdf_Const::FONT_TIMES_BOLDITALIC,
                                         Zend_Pdf_Const::FONT_HELVETICA,
                                         Zend_Pdf_Const::FONT_HELVETICA_BOLD,
                                         Zend_Pdf_Const::FONT_HELVETICA_ITALIC,
                                         Zend_Pdf_Const::FONT_HELVETICA_BOLDITALIC,
                                         Zend_Pdf_Const::FONT_COURIER,
                                         Zend_Pdf_Const::FONT_COURIER_BOLD,
                                         Zend_Pdf_Const::FONT_COURIER_ITALIC,
                                         Zend_Pdf_Const::FONT_COURIER_BOLDITALIC,
                                         Zend_Pdf_Const::FONT_SYMBOL,
                                         Zend_Pdf_Const::FONT_ZAPFDINGBATS);

    // Shape filling types
    const SHAPEDRAW_STROKE      = 0;
    const SHAPEDRAW_FILL        = 1;
    const SHAPEDRAW_FILLNSTROKE = 2;

    // Shape filling methods
    const FILLMETHOD_NONZEROWINDING = 0;
    const FILLMETHOD_EVENODD        = 1;

    // Line dashing predefined types
    const LINEDASHING_SOLID = 0;

    /**
     * Convert date to PDF format (it's close to ASN.1 (Abstract Syntax Notation One), defined in ISO/IEC 8824)
     *
     * @param int $timestamp
     */
    static public function pdfDate($timestamp = null)
    {
        $date = ($timestamp === null) ? date('\D:YmdHisO') : date('\D:YmdHisO', $timestamp);
        return substr_replace($date, '\'', -2, 0) . '\'';
    }
}

