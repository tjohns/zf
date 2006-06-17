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
 * @subpackage Core
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Exception */
require_once 'Zend/Exception.php';


/**
 * Exception class for Zend_Pdf.
 *
 * If you expect a certain type of exception to be caught and handled by the
 * caller, create a constant for it here and include it in the object being
 * thrown. Example:
 *
 *   throw new Zend_Pdf_Exception('foo() is not yet implemented',
 *                                Zend_Pdf_Exception::NOTIMPLEMENTED);
 *
 * This allows the caller to determine the specific type of exception that was
 * thrown without resorting to parsing the descriptive text.
 *
 * IMPORTANT: Do not rely on numeric values of the constants! They are grouped
 * sequentially below for organizational purposes only. The numbers may come to
 * mean something in the future, but they are subject to renumbering at any
 * time. ALWAYS use the symbolic constant names, which are guaranteed never to
 * change, in logical checks! You have been warned.
 *
 * @package    Zend_Pdf
 * @subpackage Core
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Exception extends Zend_Exception
{
  /**** Class Constants ****/
  
  
  /* Generic Exceptions */
  
    /**
     * The feature or option is planned but has not yet been implemented. It
     * should be available in a future revision of the framework.
     */
    const NOTIMPLEMENTED = 0x0001;
    
    /**
     * The feature or option has been deprecated and will be removed in a future
     * revision of the framework. The descriptive text accompanying this 
     * exception should explain how to use the replacement features or options.
     */
    const DEPRECATED = 0x0002;
    
    /**
     * Not enough paramaters were supplied to the method.
     */
    const TOOFEWPARAMETERS = 0x0003;
    
    /**
     * A parameter was of the wrong data type.
     */
    const BADPARAMETERTYPE = 0x0004;
    
    /**
     * A parameter contained an unusable value.
     */
    const BADPARAMETERVALUE = 0x0005;
    
    /**
     * A parameter value was not within the expected range.
     */
    const PARAMETERVALUEOUTOFRANGE = 0x0006;

    /**
     * The method that has multiple signatures could not understand the
     * number and/or types of parameters.
     */
    const BADMETHODSIGNATURE = 0x0007;

    /**
     * An array or string index was out of range.
     */
    const INDEXOUTOFRANGE = 0x0008;



  /* Filesystem I/O */

    /**
     * The file path was unusable or invalid.
     */
    const BADFILEPATH = 0x0101;

    /**
     * The file is not readable by the current user.
     */
    const NOTREADABLE = 0x0102;

    /**
     * The file is not writeable by the current user.
     */
    const NOTWRITEABLE = 0x0103;

    /**
     * The file resource has been closed unexpectedly.
     */
    const FILENOTOPEN = 0x0104;

    /**
     * An error was encountered while attempting to open the file.
     */
    const CANTOPENFILE = 0x0105;

    /**
     * An error was encountered while attempting to obtain the current file
     * position.
     */
    const CANTGETFILEPOSITION = 0x0106;

    /**
     * An error was encountered while attempting to set a new file position.
     */
    const CANTSETFILEPOSITION = 0x0107;

    /**
     * An attempt was made to move the current file position before the start
     * of the file.
     */
    const MOVEBEFORESTARTOFFILE = 0x0108;

    /**
     * An attempt was made to move the current file position beyond the end of
     * the file.
     */
    const MOVEBEYONDENDOFFILE = 0x0109;

    /**
     * An error was encountered while attempting to obtain the file size.
     */
    const CANTGETFILESIZE = 0x010a;

    /**
     * An error was encountered while attempting to read data from the file.
     */
    const ERRORDURINGREAD = 0x010b;

    /**
     * An error was encountered while attempting to write data to the file.
     */
    const ERRORDURINGWRITE = 0x010c;

    /**
     * An incompatible page size was specified for a buffered read operation.
     */
    const INVALIDPAGESIZE = 0x010d;

    /**
     * There is insufficient data to fulfill the read request.
     */
    const INSUFFICIENTDATA = 0x010e;



  /* Zend_Pdf_FileParser */

    /**
     * The file parser data source object was invalid or improperly initialized.
     */
    const BADDATASOURCE = 0x0201;

    /**
     * An unknown byte order was specified.
     */
    const INVALIDBYTEORDER = 0x0202;

    /**
     * An invalid integer size was specified.
     */
    const INVALIDINTEGERSIZE = 0x0203;

    /**
     * An invalid fixed-point number size was specified.
     */
    const BADFIXEDPOINTSIZE = 0x0204;

    /**
     * The string cannot be read.
     */
    const CANTREADSTRING = 0x0205;

    /**
     * This file type must be parsed in a specific order and a parsing method
     * was called out-of-turn.
     */
    const PARSEDOUTOFORDER = 0x0206;



  /* Zend_Pdf_FileParser_Font and Subclasses */

    /**
     * The font file type is incorrect.
     */
    const WRONGFONTTYPE = 0x0301;

    /**
     * The number of tables contained in the font is outside the expected range.
     */
    const BADTABLECOUNT = 0x0302;

    /**
     * A required table was not present in the font.
     */
    const REQUIREDTABLENOTFOUND = 0x0303;

    /**
     * The parser does not understand this version of this table in the font.
     */
    const DONTUNDERSTANDTABLEVERSION = 0x0303;

    /**
     * The magic number in the font file is incorrect.
     */
    const BADMAGICNUMBER = 0x0304;

    /**
     * Could not locate a usable character map for this font.
     */
    const CANTFINDGOODCMAP = 0x0305;



  /* Zend_Pdf_Cmap and Subclasses */

    /**
     * The character map type is currently unsupported.
     */
    const CMAPTYPEUNSUPPORTED = 0x0401;

    /**
     * The type of the character map is not understood.
     */
    const CMAPUNKNOWNTYPE = 0x0402;

    /**
     * The character map table data is too small.
     */
    const CMAPTABLEDATATOOSMALL = 0x0403;

    /**
     * The character map table data is for a different type of table.
     */
    const CMAPWRONGTABLETYYPE = 0x0404;

    /**
     * The character map table data contains in incorrect length.
     */
    const CMAPWRONGTABLELENGTH = 0x0405;

    /**
     * This character map table is language-dependent. Character maps must be
     * language-independent.
     */
    const CMAPNOTLANGUAGEINDEPENDENT = 0x0406;

    /**
     * The final byte offset when reading the character map table data does not
     * match the reported length of the table.
     */
    const CMAPFINALOFFSETNOTLENGTH = 0x0407;

    /**
     * The character map subtable entry count does not match the expected value.
     */
    const CMAPWRONGENTRYCOUNT = 0x0408;



  /* Zend_Pdf_Resource_Font and Subclasses */

    /**
     * The specified glyph number is out of range for this font.
     */
    const GLYPHOUTOFRANGE = 0x0501;

    /**
     * This font program has copyright bits set which prevent it from being
     * embedded in the PDF file. You must specify the no-embed option to use
     * this font.
     */
    const FONTCANNOTBEEMBEDDED = 0x0502;



  /* Zend_Pdf_Font */

    /**
     * The font name did not match any previously instantiated font and is not
     * one of the standard 14 PDF fonts.
     */
    const BADFONTNAME = 0x0601;

    /**
     * The factory method could not determine the type of the font file.
     */
    const CANTDETERMINEFONTTYPE = 0x0602;



  /* Text Layout System */

    /**
     * The specified attribute value for the text object cannot be used.
     */
    const BADATTRIBUTEVALUE = 0x0701;


}
